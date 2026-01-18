import axios from "axios";
import React, { useState, useEffect } from "react";
import toast, { Toaster } from "react-hot-toast";
import Swal from "sweetalert2";
import SuccessSound from "../sounds/beep-07a.mp3";
import WarningSound from "../sounds/beep-02.mp3";
import playSound from "../utils/playSound";

export default function Cart({ carts, setCartUpdated, cartUpdated }) {
    function increment(id) {
        axios
            .put("/admin/cart/increment", {
                id: id,
            })
            .then((res) => {
                setCartUpdated(!cartUpdated);
                playSound(SuccessSound);
                toast.success(res?.data?.message);
            })
            .catch((err) => {
                playSound(WarningSound);
                toast.error(err.response.data.message);
            });
    }
    function decrement(id) {
        axios
            .put("/admin/cart/decrement", {
                id: id,
            })
            .then((res) => {
                setCartUpdated(!cartUpdated);
                playSound(SuccessSound);
                toast.success(res?.data?.message);
            })
            .catch((err) => {
                playSound(WarningSound);
                toast.error(err.response.data.message);
            });
    }

    // Direct quantity update for weight-based selling
    function updateQuantity(id, newQuantity) {
        const qty = parseFloat(newQuantity);
        if (isNaN(qty) || qty <= 0) {
            toast.error("Enter valid quantity");
            return;
        }
        axios
            .put("/admin/cart/update-quantity", {
                id: id,
                quantity: qty,
            })
            .then((res) => {
                setCartUpdated(!cartUpdated);
                playSound(SuccessSound);
                toast.success(res?.data?.message);
            })
            .catch((err) => {
                playSound(WarningSound);
                toast.error(err.response?.data?.message || "Update failed");
            });
    }

    // Update by price - customer says "100 rupees ka dedo"
    // System auto-calculates: 100 ÷ 1400 = 0.071 kg
    function updateByPrice(id, desiredPrice) {
        const price = parseFloat(desiredPrice);
        if (isNaN(price) || price <= 0) {
            toast.error("Enter valid amount");
            return;
        }
        axios
            .put("/admin/cart/update-by-price", {
                id: id,
                price: price,
            })
            .then((res) => {
                setCartUpdated(!cartUpdated);
                playSound(SuccessSound);
                toast.success(res?.data?.message);
            })
            .catch((err) => {
                playSound(WarningSound);
                toast.error(err.response?.data?.message || "Update failed");
            });
    }

    function destroy(id) {
        Swal.fire({
            title: "Are you sure you want to delete this item?",
            showDenyButton: true,
            confirmButtonText: "Yes",
            denyButtonText: "No",
            customClass: {
                actions: "my-actions",
                cancelButton: "order-1 right-gap",
                confirmButton: "order-2",
                denyButton: "order-3",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .put("/admin/cart/delete", {
                        id: id,
                    })
                    .then((res) => {
                        console.log(res);
                        setCartUpdated(!cartUpdated);
                        playSound(SuccessSound);
                        toast.success(res?.data?.message);
                    })
                    .catch((err) => {
                        toast.error(err.response.data.message);
                    });
            } else if (result.isDenied) {
                return;
            }
        });
    }
    return (
        <>
            <div className="user-cart">
                <div className="card">
                    <div className="card-body">
                        <div className="responsive-table">
                            <table className="table table-striped">
                                <thead>
                                    <tr className="text-center">
                                        <th>Name</th>
                                        <th>Qty (kg/pcs)</th>
                                        <th></th>
                                        <th>Price/Unit</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {carts.map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.product.name}</td>
                                            <td className="d-flex align-items-center">
                                                <button
                                                    className="btn btn-warning btn-sm"
                                                    onClick={() =>
                                                        decrement(item.id)
                                                    }
                                                >
                                                    <i className="fas fa-minus"></i>
                                                </button>
                                                <input
                                                    key={`qty-${item.id}-${item.quantity}`}
                                                    type="number"
                                                    className="form-control form-control-sm qty ml-1 mr-1"
                                                    defaultValue={item.quantity}
                                                    step="0.001"
                                                    min="0.001"
                                                    style={{ width: '80px' }}
                                                    onBlur={(e) => {
                                                        if (e.target.value !== String(item.quantity)) {
                                                            updateQuantity(item.id, e.target.value);
                                                        }
                                                    }}
                                                    onKeyDown={(e) => {
                                                        if (e.key === 'Enter') {
                                                            e.target.blur();
                                                        }
                                                    }}
                                                />
                                                <button
                                                    className="btn btn-success btn-sm"
                                                    onClick={() =>
                                                        increment(item.id)
                                                    }
                                                >
                                                    <i className="fas fa-plus "></i>
                                                </button>
                                            </td>
                                            <td>
                                                <button
                                                    className="btn btn-danger btn-sm mr-3"
                                                    onClick={() =>
                                                        destroy(item.id)
                                                    }
                                                >
                                                    <i className="fas fa-trash "></i>
                                                </button>
                                            </td>
                                            <td className="text-right">
                                                {item?.product?.discounted_price}
                                                {item?.product?.price >
                                                    item?.product
                                                        ?.discounted_price ? (
                                                    <>
                                                        <br />
                                                        <del>
                                                            {item?.product?.price}
                                                        </del>
                                                    </>
                                                ) : (
                                                    ""
                                                )}
                                            </td>
                                            <td className="text-right">
                                                <input
                                                    key={`total-${item.id}-${item.row_total}`}
                                                    type="number"
                                                    className="form-control form-control-sm text-right"
                                                    defaultValue={item.row_total}
                                                    step="1"
                                                    min="1"
                                                    style={{ width: '90px', display: 'inline-block' }}
                                                    title="Enter Rs. amount"
                                                    onBlur={(e) => {
                                                        if (e.target.value !== String(item.row_total)) {
                                                            updateByPrice(item.id, e.target.value);
                                                        }
                                                    }}
                                                    onKeyDown={(e) => {
                                                        if (e.key === 'Enter') {
                                                            e.target.blur();
                                                        }
                                                    }}
                                                />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <Toaster position="top-right" reverseOrder={false} />
        </>
    );
}
