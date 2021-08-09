@extends('layout')

@section('content')
    @php
    $range = range(1, request('limit', 3));
    @endphp
    <div class="flex justify-end">
        <div class="mr-2"><input id="inp-limit" type="text" class="form-control" placeholder="Số lượng bệnh viện" value="{{request('limit')}}"></div>
        <a href="/ketqua?export=true" class="btn-save btn btn-success mb-6">Tải xuống</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Danh mục kỹ thuật</th>
            @foreach($range as $i)
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
                @foreach($range as $i1)
                    @php $price = data_get($i, 'gia_'.$i1) @endphp
                    <td>{{data_get($i, 'ten_bv_'.$i1)}}</td>
                    <td>{{$price ? number_format($price) : ''}}</td>
                @endforeach

            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@push('scripts')
    <script>
        $(function (){
            $('#inp-limit').change((e) => {
                let limit = e.target.value || 10
                window.location = '/ketqua?limit='+limit
            })
        })
    </script>
@endpush
