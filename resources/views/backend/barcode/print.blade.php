<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .barcode-container {
            text-align: center;
            width: 300px; /* Standard label width approx */
            padding: 10px;
        }
        .label-text {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        @media print {
            body { 
                height: auto; 
                display: block;
            }
            .no-print { display: none; }
            @page {
                size: auto;
                margin: 0mm;
            }
        }
    </style>
    <!-- Use JsBarcode because it is easy to use in standard HTML without React -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div class="barcode-container">
        <div class="label-text">{{ $label }}</div>
        <svg id="barcode"></svg>
    </div>

    <script>
        JsBarcode("#barcode", "{{ $barcode }}", {
            format: "EAN13",
            lineColor: "#000",
            width: 2,
            height: 50,
            displayValue: true
        });
        
        window.onload = function() {
            window.print();
            // Optional: Close after print? 
            // window.close(); 
        }
    </script>
</body>
</html>
