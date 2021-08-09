@extends('layout')

@section('content')
    <div class="flex justify-end">
        <a href="/ketqua?export=true" class="btn-save btn btn-success mb-6">Tải xuống</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Danh mục kỹ thuật</th>
            @foreach([1,2,3] as $i)
                <th>BV</th>
                <th>Giá</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($data as $k => $i)
            <tr>
                <td>{{$k+1}}</td>
                <td>{{$i['ten_dm']}}</td>
                @foreach([1,2,3] as $i1)
                    @php $price = data_get($i, 'gia_'.$i1) @endphp
                    <td>{{data_get($i, 'ten_bv_'.$i1)}}</td>
                    <td>{{$price ? number_format($price) : ''}}</td>
                @endforeach

            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
