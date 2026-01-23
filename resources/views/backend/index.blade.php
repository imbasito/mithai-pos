@extends('backend.master')

@section('title', 'Dashboard')

@section('content')
<section class="content">
    @can('dashboard_view')
    <div class="container-fluid">
        <!-- Dashboard Stats Row 1: Primary KPIs with Vibrant Gradients -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box bg-gradient-navy shadow-sm">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cash-register"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-uppercase font-weight-bold" style="font-size: 0.75rem; opacity: 0.9;">Total Revenue</span>
                        <span class="info-box-number" style="font-size: 1.5rem;">
                            {{currency()->symbol??''}} {{number_format($total,2,'.',',')}}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box bg-gradient-maroon shadow-sm text-white">
                    <span class="info-box-icon bg-light elevation-1 text-maroon"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-uppercase font-weight-bold" style="font-size: 0.75rem; opacity: 0.9;">Total Profit</span>
                        <span class="info-box-number" style="font-size: 1.5rem;">
                            {{currency()->symbol??''}} {{number_format($total_profit,2,'.',',')}}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box bg-gradient-success shadow-sm">
                    <span class="info-box-icon bg-white elevation-1 text-success"><i class="fas fa-shopping-basket"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-uppercase font-weight-bold" style="font-size: 0.75rem; opacity: 0.8;">Sale Subtotal</span>
                        <span class="info-box-number" style="font-size: 1.5rem;">
                            {{currency()->symbol??''}} {{number_format($sub_total,2,'.',',')}}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box bg-gradient-warning shadow-sm">
                    <span class="info-box-icon bg-white elevation-1 text-warning"><i class="fas fa-hand-holding-usd"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-uppercase font-weight-bold" style="font-size: 0.75rem; opacity: 0.8;">Total Due</span>
                        <span class="info-box-number" style="font-size: 1.5rem;">
                            {{currency()->symbol??''}} {{number_format($due,2,'.',',')}}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Stats Row 2: Secondary Counters -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white shadow-sm border-left border-info" style="border-left-width: 4px !important;">
                    <div class="inner">
                        <h3 class="text-info">{{$total_customer}}</h3>
                        <p class="text-muted font-weight-bold">Registered Customers</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users" style="opacity: 0.1;"></i>
                    </div>
                    <a href="{{route('backend.admin.customers.index')}}" class="small-box-footer bg-light text-info border-top">
                        Manage Customers <i class="fas fa-arrow-right ml-1" style="font-size: 0.8rem;"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white shadow-sm border-left border-success" style="border-left-width: 4px !important;">
                    <div class="inner">
                        <h3 class="text-success">{{$total_product}}</h3>
                        <p class="text-muted font-weight-bold">Total Products</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box-open" style="opacity: 0.1;"></i>
                    </div>
                    <a href="{{route('backend.admin.products.index')}}" class="small-box-footer bg-light text-success border-top">
                        Inventory List <i class="fas fa-arrow-right ml-1" style="font-size: 0.8rem;"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-white shadow-sm border-left border-maroon" style="border-left-width: 4px !important;">
                    <div class="inner">
                        <h3 class="text-maroon">{{$total_order}}</h3>
                        <p class="text-muted font-weight-bold">Total Sales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-receipt" style="opacity: 0.1;"></i>
                    </div>
                    <a href="{{route('backend.admin.orders.index')}}" class="small-box-footer bg-light text-maroon border-top">
                        Recent Invoices <i class="fas fa-arrow-right ml-1" style="font-size: 0.8rem;"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-white shadow-sm border-left border-warning" style="border-left-width: 4px !important;">
                    <div class="inner">
                        <h3 class="text-warning text-maroon">{{$low_stock_products->count()}}</h3>
                        <p class="text-muted font-weight-bold">Low Stock Items</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle" style="opacity: 0.1;"></i>
                    </div>
                    <a href="{{route('backend.admin.products.index')}}" class="small-box-footer bg-light text-warning border-top">
                        Restock Now <i class="fas fa-arrow-right ml-1" style="font-size: 0.8rem;"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /.row -->


        <!-- Recent Activity & Detailed Analytics -->
        <div class="row mt-4">
            <!-- Sales Trends (Interactive Chart) -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-weight-bold mb-0">
                                <i class="fas fa-chart-area mr-2 text-primary"></i>Sales Performance
                            </h5>
                            <div class="input-group w-auto bg-light rounded-pill px-2" style="border: 1px solid #eee;">
                                <i class="far fa-calendar-alt mt-2 mr-2 text-muted"></i>
                                <input type="text" class="form-control border-0 bg-transparent py-0" id="reservation" style="width: 180px; font-size: 0.85rem;" placeholder="Filter dates...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="salesChart" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-crown mr-2 text-warning"></i>Top Selling
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-valign-middle mb-0">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.75rem; text-uppercase; letter-spacing: 1px;">
                                        <th>Product</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Sold</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($top_products as $tp)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/' . $tp->image) }}" class="rounded mr-2" style="width: 32px; height: 32px; object-fit: cover;" onerror="this.src='{{ asset('assets/images/no-image.png') }}'">
                                                <span class="font-weight-bold text-truncate" style="max-width: 120px;">{{ $tp->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-right font-weight-bold">${{ number_format($tp->discounted_price, 0) }}</td>
                                        <td class="text-right">
                                            <span class="badge badge-pill badge-info px-2">{{ $tp->sold_qty ?? 0 }} sold</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-center border-0">
                        <a href="{{ route('backend.admin.products.index') }}" class="small font-weight-bold text-primary">View All Products <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Low Stock Alerts -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
                        <h5 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-exclamation-circle mr-2 text-danger"></i>Low Stock Inventory
                        </h5>
                        <span class="badge badge-danger">Critical</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light text-muted" style="font-size: 0.7rem;">
                                <tr>
                                    <th class="pl-3">Product Name</th>
                                    <th>Sku/Barcode</th>
                                    <th class="text-right pr-3">In Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($low_stock_products as $lsp)
                                <tr>
                                    <td class="pl-3 py-2">
                                        <div class="font-weight-bold">{{ $lsp->name }}</div>
                                    </td>
                                    <td><code>{{ $lsp->sku }}</code></td>
                                    <td class="text-right pr-3">
                                        <b class="text-danger">{{ $lsp->quantity }}</b> 
                                        <small class="text-muted">{{ $lsp->unit->short_name ?? '' }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">All products are healthy in stock.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Yearly Bar Chart -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-chart-bar mr-2 text-success"></i>Monthly Growth ({{ $currentYear }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="yearlyChart" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    <!-- /.container-fluid -->
</section>
@endsection

@push('style')
<style>
    .info-box { border-radius: 15px; border: none; }
    .small-box { border-radius: 15px; }
    .bg-gradient-navy { background: linear-gradient(135deg, #001f3f, #004080); color: white; }
    .bg-gradient-maroon { background: linear-gradient(135deg, #800000, #b30000); color: white; }
    .bg-gradient-success { background: linear-gradient(135deg, #28a745, #4cd137); color: white; }
    .bg-gradient-warning { background: linear-gradient(135deg, #f39c12, #f1c40f); color: white; }
</style>
@endpush

@push('script')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Sales performance chart (Interactive Line/Area)
    var salesOptions = {
        series: [{
            name: 'Total Sales',
            data: @json($totalAmounts)
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'inherit'
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#007bff'],
        xaxis: {
            categories: @json($dates),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (value) { return "Rs." + value; }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4
        }
    };

    var salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
    salesChart.render();

    // Yearly Monthly Growth Chart (Bar)
    var yearlyOptions = {
        series: [{
            name: 'Monthly Revenue',
            data: @json($totalAmountMonth)
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#28a745'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '55%',
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4
        }
    };

    var yearlyChart = new ApexCharts(document.querySelector("#yearlyChart"), yearlyOptions);
    yearlyChart.render();

    $(function() {
        //Date range picker
        $('#reservation').daterangepicker().on('apply.daterangepicker', function(e, picker) {
            let selectedDateRange = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');
            let url = new URL(window.location.href);
            url.searchParams.set('daterange', selectedDateRange);
            window.location.href = url.toString();
        });
    });
</script>
@endpush