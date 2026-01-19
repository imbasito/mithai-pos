const { app, BrowserWindow, dialog } = require('electron');
const { autoUpdater } = require('electron-updater');
const { spawn } = require('child_process');
const path = require('path');
const net = require('net');
const http = require('http');
const treeKill = require('tree-kill');

let laravelServer, mysqlServer, splashWindow, mainWindow;
const MYSQL_PORT = 3307;
let laravelPort = 8000;

const basePath = app.isPackaged ? process.resourcesPath : __dirname;

// ============================================
// HEALTH CHECK UTILITIES
// ============================================
function waitForMySQL(port, timeout = 30000) {
    return new Promise((resolve, reject) => {
        const startTime = Date.now();
        console.log(`Waiting for MySQL on port ${port}...`);
        
        const check = () => {
            const socket = new net.Socket();
            socket.setTimeout(2000);
            
            socket.on('connect', () => {
                console.log('MySQL connection successful!');
                socket.destroy();
                resolve(true);
            });
            
            socket.on('error', () => {
                socket.destroy();
                if (Date.now() - startTime > timeout) {
                    reject(new Error(`MySQL connection timeout after ${timeout/1000}s`));
                } else {
                    setTimeout(check, 1000);
                }
            });
            
            socket.on('timeout', () => {
                socket.destroy();
                if (Date.now() - startTime > timeout) {
                    reject(new Error(`MySQL connection timeout after ${timeout/1000}s`));
                } else {
                    setTimeout(check, 1000);
                }
            });
            
            socket.connect(port, '127.0.0.1');
        };
        
        setTimeout(check, 2000);
    });
}

function waitForLaravel(port, timeout = 30000) {
    return new Promise((resolve, reject) => {
        const startTime = Date.now();
        console.log(`Waiting for Laravel on port ${port}...`);
        
        const check = () => {
            const req = http.get(`http://127.0.0.1:${port}`, () => {
                console.log('Laravel connection successful!');
                resolve(true);
            });
            
            req.on('error', () => {
                if (Date.now() - startTime > timeout) {
                    reject(new Error(`Laravel connection timeout after ${timeout/1000}s`));
                } else {
                    setTimeout(check, 1000);
                }
            });
            
            req.setTimeout(3000, () => {
                req.destroy();
                if (Date.now() - startTime > timeout) {
                    reject(new Error(`Laravel connection timeout after ${timeout/1000}s`));
                } else {
                    setTimeout(check, 1000);
                }
            });
        };
        
        setTimeout(check, 2000);
    });
}

// ============================================
// SPLASH SCREEN
// ============================================
function createSplashWindow() {
    splashWindow = new BrowserWindow({
        width: 600,
        height: 450,
        frame: false,
        transparent: true,
        alwaysOnTop: true,
        resizable: false,
        skipTaskbar: false,
        icon: path.join(basePath, 'pos-icon.ico'),
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        }
    });

    const splashPath = app.isPackaged
        ? path.join(process.resourcesPath, 'splash.html')
        : path.join(__dirname, 'splash.html');

    splashWindow.loadFile(splashPath);
    splashWindow.center();
}

function updateSplashStatus(message) {
    if (splashWindow && !splashWindow.isDestroyed()) {
        splashWindow.webContents.executeJavaScript(
            `if(document.getElementById('status-text')) document.getElementById('status-text').innerText = '${message}';`
        ).catch(() => {});
    }
}

// ============================================
// SERVER STARTUP
// ============================================
function startMySQL() {
    return new Promise((resolve) => {
        const mysqlPath = path.join(basePath, 'mysql', 'bin', 'mysqld.exe');
        const myIniPath = path.join(basePath, 'mysql', 'my.ini');

        console.log('Starting MySQL server...');

        mysqlServer = spawn(mysqlPath, [`--defaults-file=${myIniPath}`, '--console'], {
            cwd: basePath,
            windowsHide: true
        });

        mysqlServer.stdout.on('data', data => console.log(`[MySQL] ${data}`));
        mysqlServer.stderr.on('data', data => console.log(`[MySQL] ${data}`));

        resolve();
    });
}

function startLaravel(port) {
    return new Promise((resolve) => {
        const phpPath = path.join(basePath, 'php', 'php.exe');

        console.log(`Starting Laravel server on port ${port}...`);

        laravelServer = spawn(phpPath, ['artisan', 'serve', `--host=127.0.0.1`, `--port=${port}`], {
            cwd: basePath,
            windowsHide: true
        });

        laravelServer.stdout.on('data', data => console.log(`[Laravel] ${data}`));
        laravelServer.stderr.on('data', data => console.log(`[Laravel] ${data}`));

        resolve();
    });
}

// ============================================
// MAIN WINDOW
// ============================================
function createMainWindow() {
    mainWindow = new BrowserWindow({
        width: 1280,
        height: 800,
        title: "Mithai POS - Point of Sale System",
        icon: path.join(basePath, 'pos-icon.ico'),
        autoHideMenuBar: true,
        show: false,
        backgroundColor: '#FFFDF9',
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.cjs')
        }
    });

    mainWindow.loadURL(`http://127.0.0.1:${laravelPort}`);

    mainWindow.webContents.on('did-finish-load', () => {
        if (splashWindow && !splashWindow.isDestroyed()) {
            splashWindow.close();
        }
        mainWindow.show();
        mainWindow.maximize();
        mainWindow.focus();
        mainWindow.setAlwaysOnTop(true);
        setTimeout(() => mainWindow.setAlwaysOnTop(false), 300);
    });

    mainWindow.on('closed', () => {
        killProcesses();
        app.quit();
    });
}

// ============================================
// AUTO-UPDATER
// ============================================
function setupAutoUpdater() {
    // Initial silent check
    if (app.isPackaged) {
        autoUpdater.checkForUpdatesAndNotify();
    }

    // IPC Listeners
    ipcMain.on('updater:check', () => {
        autoUpdater.checkForUpdates()
            .then(result => {
                if (!result || !result.updateInfo || result.updateInfo.version === app.getVersion()) {
                    mainWindow.webContents.send('updater:status', 'latest');
                }
            })
            .catch(err => {
                console.error('Check for updates error:', err);
                mainWindow.webContents.send('updater:status', 'error');
            });
    });

    ipcMain.on('updater:download', () => {
        autoUpdater.downloadUpdate();
    });

    ipcMain.on('updater:install', () => {
        killProcesses();
        autoUpdater.quitAndInstall();
    });

    // Event Handlers
    autoUpdater.on('update-available', (info) => {
        mainWindow.webContents.send('updater:status', 'available', info);
    });

    autoUpdater.on('update-not-available', () => {
        mainWindow.webContents.send('updater:status', 'latest');
    });

    autoUpdater.on('download-progress', (progressObj) => {
        mainWindow.webContents.send('updater:progress', progressObj);
    });

    autoUpdater.on('update-downloaded', (info) => {
        mainWindow.webContents.send('updater:ready', info);
    });

    autoUpdater.on('error', (err) => {
        console.error('Auto-updater error:', err);
        mainWindow.webContents.send('updater:status', 'error', err.message);
    });
}

// ============================================
// ERROR HANDLING
// ============================================
function showErrorDialog(title, message) {
    return dialog.showMessageBox({
        type: 'error',
        title: title,
        message: message,
        buttons: ['Retry', 'Exit'],
        defaultId: 0
    });
}

// ============================================
// CLEANUP
// ============================================
function killProcesses() {
    console.log('Killing processes...');
    
    if (laravelServer && !laravelServer.killed) {
        treeKill(laravelServer.pid, 'SIGKILL', err => {
            if (err) console.error('Failed to kill Laravel:', err);
            else console.log('Laravel server killed.');
        });
    }

    if (mysqlServer && !mysqlServer.killed) {
        treeKill(mysqlServer.pid, 'SIGKILL', err => {
            if (err) console.error('Failed to kill MySQL:', err);
            else console.log('MySQL server killed.');
        });
    }
}

// ============================================
// MAIN STARTUP SEQUENCE
// ============================================
async function startApp() {
    try {
        createSplashWindow();
        
        updateSplashStatus('Starting database...');
        await startMySQL();

        updateSplashStatus('Starting server...');
        await startLaravel(laravelPort);

        updateSplashStatus('Connecting to database...');
        await waitForMySQL(MYSQL_PORT, 30000);

        updateSplashStatus('Loading application...');
        await waitForLaravel(laravelPort, 30000);

        updateSplashStatus('Opening POS...');
        createMainWindow();

        // Check for updates in the background after app starts
        if (app.isPackaged) {
            setupAutoUpdater();
        }

    } catch (error) {
        console.error('Startup error:', error);
        
        if (splashWindow && !splashWindow.isDestroyed()) {
            splashWindow.hide();
        }

        const result = await showErrorDialog(
            'Startup Failed',
            `${error.message}\n\nWould you like to retry?`
        );

        if (result.response === 0) {
            killProcesses();
            setTimeout(startApp, 2000);
        } else {
            killProcesses();
            app.quit();
        }
    }
}

// ============================================
// APP LIFECYCLE
// ============================================
app.whenReady().then(startApp);

app.on('window-all-closed', () => {
    killProcesses();
    if (process.platform !== 'darwin') app.quit();
});

app.on('before-quit', () => {
    killProcesses();
});
