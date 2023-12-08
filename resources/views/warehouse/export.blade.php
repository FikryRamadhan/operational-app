<?php
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 180);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stock Gudang Export</title>
    <style type="text/css">
        *{
            font-family: Arial, Helvetica, sans-serif;
        }
        .table{
            margin-top: 10px
        }
        table{
            width: 100%;
            border: 1px solid;
            border-collapse: collapse;
            align-items: center;
        }
        .date{
            font-size: 15px;
            margin-top: -5px
        }
        th{
            border: 1px solid;
        }
        td{
            border: 1px solid;
            text-align: center;
        }
    </style>
</head>
<body>
    <center>
        <div class="head">
            <h3><strong>Laporan Stok {{ $warehouse->warehouse_name }}</strong></h3>
            <p class="date">Tanggal : {{ date('d F Y') }}</p>
        </div>
    </center>

    <div class="table">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Jenis</th>
                    <th>Stok</th>
                </tr>
            </thead>
            
            <tbody>
                @foreach ($warehouse->warehouseStock as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->getProduct() }}</td>
                        <td>{{ $item->getProductType() }}</td>
                        <td>{{ $item->stock }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
</body>
</html>