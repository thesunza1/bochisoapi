<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title style="text-align: center; font-weight: bold; font-size: 14px"> {{ $unit->name }} </title>
</head>

<body>
    <table>
        <thead>
            <tr>
                @if ($asyear == 1)

                <th colspan="7" style="text-align: center; font-weight: bold; font-size: 14px"> {{ $unit->name }} {{ ',
                     Cả năm '.$monthset->year }}</th>
                @else
                <th colspan="7" style="text-align: center; font-weight: bold; font-size: 14px"> {{ $unit->name }} {{ ',
                    tháng '.$monthset->month.' năm '.$monthset->year }}</th>
                @endif

            </tr>
            <tr>
                <th style="text-align: right" colspan="2"> NGÀY XUẤT DỮ LIỆU: </th>
                <th style="color: red ; font-weight: bold" colspan="8"> {{ $nowdate }}</th>
            </tr>
            <tr></tr>
            <tr>
                <th style="font-weight: bold; width: 50px">STT</th>
                <th style="font-weight: bold; width: 300px">CHỈ SỐ</th>
                {{-- <th style="font-weight: bold; width: 120px">CHƯA CHẤP NHẬN(%)</th>
                <th style="font-weight: bold; width: 110px">QUAN TÂM(%)</th>
                <th style="font-weight: bold; width: 110px">CHẤP NHẬN(%)</th> --}}
                <th style="font-weight: bold; width: 90px">ĐƠN VỊ TÍNH</th>
                <th style="font-weight: bold; width: 90px">KẾ HOẠCH</th>
                <th style="font-weight: bold; width: 90px">THỰC HIỆN</th>
                <th style="font-weight: bold; width: 100px">% SO KẾ HOẠCH</th>
                <th style="font-weight: bold; width: 150px">NGƯỠNG CẢNH BÁO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topics as $topic)
            <tr style="">
                <td style="text-align: left; font-weight: bold;background: #737373 ; color: white "> {{++$loop->index }}
                </td>
                <td colspan="6" style="font-weight: bold;background:  #737373; color: white"> {{$topic['name'] }}
                </td>
            </tr>
            @foreach($topic['targets'] as $targetParent)
            <tr>
                <td style="text-align: left"> {{ $loop->parent->index + 1 }} . {{ $loop->index + 1}}</td>
                <td> {{ $targetParent['name'] }}</td>
                {{-- <td> {{ " <".$targetParent['setindicators'][0]['min_warning'] }}</td>
                <td> {{ " >= ".$targetParent['setindicators'][0]['min_warning']." , <" }} {{
                        $targetParent['setindicators'][0]['plan_warning'] }} </td>
                <td> {{ " >= ".$targetParent['setindicators'][0]['plan_warning'] }}</td> --}}
                <td> {{$targetParent['comment'] }}</td>
                <td> {{ $targetParent['setindicators'][0]['plan'] }}</td>
                <td> {{ $targetParent['setindicators'][0]['total_plan'] }}</td>
                @if ( $targetParent['setindicators'][0]['plan'] == 0)
                <td> 0 </td>
                <td style="background: red"></td>
                @else
                <td> {{ round($targetParent['setindicators'][0]['total_plan']/$targetParent['setindicators'][0]['plan']
                    * 100, 2)
                    }}
                </td>
                @if( !array_search($targetParent['id'], [91,92,93]))
                @if ( $targetParent['setindicators'][0]['total_plan']/$targetParent['setindicators'][0]['plan'] * 100 <
                    $targetParent['setindicators'][0]['min_warning']) <td style="background: red">
                    </td>
                    @elseif( $targetParent['setindicators'][0]['total_plan']/$targetParent['setindicators'][0]['plan'] *
                    100
                    > $targetParent['setindicators'][0]['plan_warning'])
                    <td style="background: green "></td>
                    @else
                    <td style="background: orange"></td>
                    @endif
                    @endif

                    @endif
                    <td></td>
                    <td></td>
            </tr>
            @foreach($targetParent['targets'] as $target)
            <tr>
                <td style="text-align: left"> {{ $loop->parent->parent->index + 1 }}. {{ $loop->parent->index + 1 }} .
                    {{
                    $loop->index + 1}}</td>
                <td> {{ $target['name'] }}</td>
                {{-- <td> {{ " <".$target['setindicators'][0]['min_warning'] }}</td>
                <td> {{ " >= ".$target['setindicators'][0]['min_warning']." , <" }} {{
                        $target['setindicators'][0]['plan_warning'] }} </td>
                <td> {{ " >= ".$target['setindicators'][0]['plan_warning'] }}</td> --}}
                <td> {{$target['comment'] }}</td>
                <td> {{ $target['setindicators'][0]['plan'] }}</td>
                <td> {{ $target['setindicators'][0]['total_plan'] }}</td>
                @if ( $target['setindicators'][0]['plan'] == 0)
                <td> 0 </td>
                <td style="background: red"></td>
                @else
                <td> {{ round($target['setindicators'][0]['total_plan']/$target['setindicators'][0]['plan'] * 100, 2)
                    }}
                </td>
                @if( gettype(array_search( $target['id'], [91,92,93])) == 'boolean')
                @if ( $target['setindicators'][0]['total_plan']/$target['setindicators'][0]['plan'] * 100 <
                    $target['setindicators'][0]['min_warning']) <td style="background: red">
                    </td>
                    @elseif( $target['setindicators'][0]['total_plan']/$target['setindicators'][0]['plan'] * 100
                    > $target['setindicators'][0]['plan_warning'])
                    <td style="background: green "></td>
                    @else
                    <td style="background: orange"></td>
                    @endif

                    @else
                    @if ( $target['setindicators'][0]['total_plan']/$target['setindicators'][0]['plan'] * 100 <
                        $target['setindicators'][0]['min_warning']) <td style="background: green">
                        </td>
                        @elseif( $target['setindicators'][0]['total_plan']/$target['setindicators'][0]['plan'] * 100
                        > $target['setindicators'][0]['plan_warning'])
                        <td style="background: red "></td>
                        @else
                        <td style="background: orange"></td>
                        @endif
                        @endif
                        @endif
                        <td></td>
                        <td></td>
            </tr>
            @endforeach
            @endforeach
            @endforeach
        </tbody>
    </table>

</body>

</html>
