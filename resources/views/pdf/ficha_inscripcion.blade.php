<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Inscripción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
        }
        
        /* --- ESTILOS DEL ENCABEZADO DE LOGOS --- */
        .header-logos {
            width: 100%;
            margin-bottom: 5px;
        }
        .header-logos td {
            vertical-align: middle;
        }
        
        /* Títulos centrados */
        .header-titles {
            text-align: center;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .header-titles h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }
        .header-titles h3 {
            margin: 2px 0 0 0;
            font-size: 12px;
            font-weight: bold;
        }

        /* --- TABLA DE DATOS GENERALES --- */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            border: 1px solid #999;
            padding: 3px 5px;
        }
        .label {
            font-weight: bold;
        }

        /* --- TABLA DE CONCEPTOS --- */
        .table-conceptos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 10px;
        }
        .table-conceptos th, .table-conceptos td {
            border: 1px solid #000;
            padding: 4px;
        }
        .table-conceptos thead th {
            text-align: center;
            font-weight: bold;
            background-color: #ffffff;
            border-bottom: 2px solid #000;
        }
        
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-bold { font-weight: bold; }
        
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #000;
        }
        
        .muted { color: #aaa; }
        
        /* Checkbox Visual */
        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            line-height: 10px;
            font-size: 10px;
            margin: 0 auto;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #555;
        }
    </style>
</head>
<body>

    <table class="header-logos">
        <tr>
            <td style="text-align: left; width: 30%;">
                <img src="{{ public_path('images/zac2027.jpg') }}" style="height: 55px;" alt="Gobierno Zacatecas">
            </td>
            
            <td style="text-align: right; width: 70%;">
                <img src="{{ public_path('images/sec.png') }}" style="height: 35px; margin-right: 15px;" alt="Educación">
                <img src="{{ public_path('images/conal.png') }}" style="height: 35px;" alt="Conalep">
            </td>
        </tr>
    </table>

    <div class="header-titles">
        <h2>Formato de Cobro de Inscripción y Reinscripción</h2>
        <h3>Trámites Ordinarios</h3>
    </div>

    <table class="info-table">
        <tr>
            <td class="label" style="width: 15%;">PLANTEL:</td>
            <td style="width: 50%;">MTRA. DOLORES CASTRO VARELA</td>
            <td class="label" style="width: 15%;">FOLIO:</td>
            <td style="width: 20%;">{{ $data['folio'] }}</td>
        </tr>
        <tr>
            <td class="label">FECHA:</td>
            <td>{{ date('d/m/Y') }}</td>
            <td class="label">PERIODO:</td>
            <td>{{ $data['periodo'] }}</td>
        </tr>
        <tr>
            <td class="label">MATRÍCULA:</td>
            <td>{{ $record->matricula }}</td>
            <td class="label">GRUPO:</td>
            <td>{{ $record->group->name ?? '0' }}</td>
        </tr>
        <tr>
            <td class="label">ALUMNO:</td>
            <td colspan="3">{{ $record->nombre }} {{ $record->apellido_paterno }} {{ $record->apellido_materno }}</td>
        </tr>
    </table>

    <table class="table-conceptos">
        <thead>
            <tr>
                <th style="text-align: left; padding-left: 10px;">CONCEPTO DE PAGO:</th>
                <th style="width: 40px;">Sel.</th>
                <th style="width: 80px;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @php $totalCalculado = 0; @endphp
            
            @foreach($conceptos_procesados as $concepto)
                @php 
                    $monto = floatval($concepto['monto']);
                    $seleccionado = $concepto['seleccionado'];
                    if($seleccionado) { $totalCalculado += $monto; }
                @endphp
                
                <tr class="{{ !$seleccionado ? 'muted' : '' }}">
                    <td>{{ $concepto['nombre'] }}</td>
                    
                    <td class="text-center">
                        <div class="checkbox-box">
                            {{ $seleccionado ? 'X' : '' }}
                        </div>
                    </td>
                    
                    <td class="text-right">
                        $ {{ number_format($seleccionado ? $monto : 0, 2) }}
                    </td>
                </tr>
            @endforeach

            <tr><td colspan="3" style="height: 15px; border: none;"></td></tr>

            <tr class="total-row">
                <td style="border: none;"></td>
                <td class="text-right text-bold" style="border: 1px solid #000;">Total Pagar</td>
                <td class="text-right text-bold" style="border: 1px solid #000;">
                    $ {{ number_format($totalCalculado, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        SERVICIOS ESCOLARES
    </div>

</body>
</html>