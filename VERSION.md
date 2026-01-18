# Mithai POS - Version & Dependency Documentation

## Application Version
**Version**: 1.0.0  
**Build Date**: January 2026  
**Developer**: SINYX (think.code.sync)

---

## Core Stack

| Component | Version | Notes |
|-----------|---------|-------|
| **Laravel** | 10.x | PHP Backend Framework |
| **React** | 18.3.1 | Frontend UI Library |
| **Electron** | 37.3.1 | Desktop Application Wrapper |
| **MySQL** | 8.0+ | Database (bundled portable) |
| **Node.js** | 20.11.0 | Bundled for portable operation |
| **PHP** | 8.2.x | Bundled for portable operation |

---

## NPM Dependencies

### Production Dependencies
| Package | Version | Purpose |
|---------|---------|---------|
| axios | 1.7.7 | HTTP Client |
| react | 18.3.1 | UI Library |
| react-dom | 18.3.1 | React DOM Renderer |
| react-barcode | 1.6.1 | Barcode Generation |
| react-datepicker | 7.5.0 | Date Picker Component |
| react-select | 5.8.1 | Select Dropdown |
| react-hot-toast | 2.4.1 | Toast Notifications |
| sweetalert2 | 11.14.1 | Alert Dialogs |
| lodash | 4.17.21 | Utility Functions |
| mysql2 | 3.5.0 | MySQL Driver for Node |
| tree-kill | 1.2.2 | Process Termination |

### Development Dependencies
| Package | Version | Purpose |
|---------|---------|---------|
| electron | 37.3.1 | Desktop App Framework |
| electron-builder | 24.13.3 | Installer Builder (STABLE) |
| vite | 4.0.0 | Build Tool |
| laravel-vite-plugin | 0.7.5 | Laravel Integration |
| @vitejs/plugin-react | 4.3.2 | React Plugin for Vite |

---

## Bundled Runtimes

| Runtime | Version | Location |
|---------|---------|----------|
| Node.js | 20.11.0 | `/nodejs/` |
| PHP | 8.2.x | `/php/` |
| MySQL | 8.0.x | `/mysql/` |

---

## Important Notes

1. **Electron Builder**: Use version **24.x** for stability. Version 26.x has compatibility issues.
2. **Node.js**: Bundled Node.js is used for portable deployment. Do NOT rely on system Node.js.
3. **Database Port**: MySQL runs on port **3307** (not default 3306) to avoid conflicts.

---

## How to Update Dependencies

```bash
# Update carefully - test after each update
.\nodejs\npm.cmd update <package-name>

# Lock versions in package.json to prevent accidental updates
# Use exact versions (no ^ or ~) for critical packages
```

---

*Last Updated: January 2026*
