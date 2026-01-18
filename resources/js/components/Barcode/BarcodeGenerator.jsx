import React, { useState, useEffect, useCallback } from "react";
import Barcode from "react-barcode";
import Swal from "sweetalert2";
import axios from "axios";

// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

export default function BarcodeGenerator() {
    const [label, setLabel] = useState("");
    const [barcodeValue, setBarcodeValue] = useState("");
    const [history, setHistory] = useState([]);
    const [loading, setLoading] = useState(false);
    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);

    // Generate random 12-digit barcode
    const generateNewBarcode = useCallback(() => {
        const randomEan = Math.floor(Math.random() * 1000000000000)
            .toString()
            .padStart(12, "0");
        setBarcodeValue(randomEan);
    }, []);

    // Load history from API
    const loadHistory = useCallback(async (page = 1) => {
        setLoading(true);
        try {
            const res = await axios.get(`/admin/barcode/history?page=${page}`);
            setHistory(res.data.data || []);
            setCurrentPage(res.data.current_page || 1);
            setLastPage(res.data.last_page || 1);
        } catch (error) {
            // Silently fail - table might not exist yet
            console.log("History not available yet");
            setHistory([]);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        generateNewBarcode();
        loadHistory();
    }, []);

    // Print barcode (and save to history)
    const handlePrint = async () => {
        if (!barcodeValue) return;

        // Open print window first (user gets immediate feedback)
        const url = `/admin/barcode/print?label=${encodeURIComponent(label)}&barcode=${barcodeValue}`;
        let iframe = document.getElementById('print-frame');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'print-frame';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }
        iframe.src = url;

        // Save to history (silently in background)
        try {
            await axios.post("/admin/barcode/store", {
                barcode: barcodeValue,
                label: label || null,
            });
            loadHistory(); // Refresh history
        } catch (error) {
            // Don't show error to user - printing still worked
            console.log("Could not save to history:", error.response?.data?.message || error.message);
        }

        // Success feedback
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Sent to printer',
            showConfirmButton: false,
            timer: 1500
        });

        // Clear and generate new for next item
        setLabel("");
        generateNewBarcode();
    };

    // Reprint from history
    const handleReprint = (item) => {
        const url = `/admin/barcode/print?label=${encodeURIComponent(item.label || '')}&barcode=${item.barcode}`;
        let iframe = document.getElementById('print-frame');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'print-frame';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }
        iframe.src = url;

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Sent to printer',
            showConfirmButton: false,
            timer: 1500
        });
    };

    // Delete from history
    const handleDelete = async (id) => {
        const result = await Swal.fire({
            title: 'Delete this barcode?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#800000',
            cancelButtonColor: '#6D4C41',
            confirmButtonText: 'Yes, delete'
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/admin/barcode/${id}`);
                loadHistory(currentPage);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Deleted',
                    showConfirmButton: false,
                    timer: 1500
                });
            } catch (error) {
                Swal.fire("Error", "Failed to delete", "error");
            }
        }
    };

    // Format date
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
    };

    return (
        <div className="row">
            {/* Left Panel - Generator */}
            <div className="col-lg-5 col-md-6 mb-4">
                <div className="card">
                    <div className="card-header">
                        <h3 className="card-title"><i className="fas fa-barcode mr-2"></i>Generate Barcode</h3>
                    </div>
                    <div className="card-body">
                        {/* Preview */}
                        <div className="text-center mb-4 p-3 border rounded" style={{ backgroundColor: '#FFFDF9', minHeight: '160px', display: 'flex', flexDirection: 'column', justifyContent: 'center', alignItems: 'center' }}>
                            <div style={{ padding: '10px', background: 'white', display: 'inline-block', textAlign: 'center', borderRadius: '8px', boxShadow: '0 2px 8px rgba(0,0,0,0.1)' }}>
                                <div style={{ fontSize: '13px', fontWeight: 'bold', marginBottom: '4px', color: '#3E2723' }}>{label || 'Product Label'}</div>
                                <Barcode
                                    value={barcodeValue || "000000000000"}
                                    format="EAN13"
                                    width={2}
                                    height={45}
                                    fontSize={11}
                                />
                            </div>
                        </div>

                        {/* Inputs */}
                        <div className="form-group">
                            <label><i className="fas fa-tag mr-1"></i> Product Label</label>
                            <input
                                type="text"
                                className="form-control"
                                placeholder="e.g. Gulab Jamun 500g"
                                value={label}
                                onChange={(e) => setLabel(e.target.value)}
                            />
                        </div>

                        <div className="form-group">
                            <label><i className="fas fa-hashtag mr-1"></i> Barcode</label>
                            <input
                                type="text"
                                className="form-control"
                                value={barcodeValue}
                                readOnly
                                style={{ backgroundColor: '#f5f5f5', fontFamily: 'monospace' }}
                            />
                        </div>

                        {/* Buttons */}
                        <div className="row mt-3">
                            <div className="col-4">
                                <button
                                    className="btn btn-secondary btn-block"
                                    onClick={() => { setLabel(""); generateNewBarcode(); }}
                                    title="Generate new barcode"
                                >
                                    <i className="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <div className="col-8">
                                <button
                                    className="btn btn-primary btn-block"
                                    onClick={handlePrint}
                                >
                                    <i className="fas fa-print mr-2"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Right Panel - History */}
            <div className="col-lg-7 col-md-6">
                <div className="card">
                    <div className="card-header">
                        <h3 className="card-title"><i className="fas fa-history mr-2"></i>Print History</h3>
                        <div className="card-tools">
                            <button className="btn btn-tool" onClick={() => loadHistory(currentPage)} title="Refresh">
                                <i className="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div className="card-body p-0">
                        {loading ? (
                            <div className="text-center py-4">
                                <i className="fas fa-spinner fa-spin fa-2x"></i>
                            </div>
                        ) : history.length === 0 ? (
                            <div className="text-center py-4 text-muted">
                                <i className="fas fa-inbox fa-3x mb-2"></i>
                                <p className="mb-0">No barcodes printed yet</p>
                                <small>Printed barcodes will appear here</small>
                            </div>
                        ) : (
                            <table className="table table-hover table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Barcode</th>
                                        <th>Date</th>
                                        <th style={{ width: '90px' }}>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {history.map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.label || <span className="text-muted">-</span>}</td>
                                            <td><code style={{ fontSize: '11px' }}>{item.barcode}</code></td>
                                            <td><small>{formatDate(item.created_at)}</small></td>
                                            <td>
                                                <button
                                                    className="btn btn-xs btn-info mr-1"
                                                    onClick={() => handleReprint(item)}
                                                    title="Reprint"
                                                >
                                                    <i className="fas fa-print"></i>
                                                </button>
                                                <button
                                                    className="btn btn-xs btn-danger"
                                                    onClick={() => handleDelete(item.id)}
                                                    title="Delete"
                                                >
                                                    <i className="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                    {/* Pagination */}
                    {lastPage > 1 && (
                        <div className="card-footer clearfix py-2">
                            <ul className="pagination pagination-sm m-0 float-right">
                                <li className={`page-item ${currentPage === 1 ? 'disabled' : ''}`}>
                                    <button className="page-link" onClick={() => loadHistory(currentPage - 1)}>«</button>
                                </li>
                                {[...Array(Math.min(lastPage, 5))].map((_, i) => (
                                    <li key={i} className={`page-item ${currentPage === i + 1 ? 'active' : ''}`}>
                                        <button className="page-link" onClick={() => loadHistory(i + 1)}>{i + 1}</button>
                                    </li>
                                ))}
                                <li className={`page-item ${currentPage === lastPage ? 'disabled' : ''}`}>
                                    <button className="page-link" onClick={() => loadHistory(currentPage + 1)}>»</button>
                                </li>
                            </ul>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
