@extends('layout')

@section('content')
    <div class="flex justify-end mb-6">
        <div class="mr-2"><input id="inp-limit" type="text" class="form-control" placeholder="Giới hạn tìm kiếm" value="{{request('limit')}}"></div>
        <a href="/xoa-kq" class="btn-save btn btn-danger mr-2">Xóa</a>
        <button class="btn-save btn btn-success">Lưu kết quả</button>
    </div>
    <form id="dm-form" action="/save-kq" method="POST" x-data="kqApp()">
        @csrf
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th style="width: 20%">Danh mục KT</th>
                <th style="width: 20%">Keywords</th>
                <th style="width: 40%">Tên danh mục</th>
                <th style="width: 20%">Bệnh viện</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(i, k) in items">
                <tr>
                    <template x-if="i.line==1"><td class="font-bold" :rowspan="i.count" x-text="i.index+1"></td></template>
                    <template x-if="i.line==1"><td class="font-bold" :rowspan="i.count" x-text="i.ten_dm_kt" ></td></template>
                    <template x-if="i.line==1">
                        <td :rowspan="i.count">
                            <textarea :name="`kqs[${i.id}][keywords]`" class="form-control inp-keywords" rows="5" x-text="i.keywords" @change="e => onChange(e, i.index)"></textarea>
                            <template x-if="i.loading">
                                <div>Loading...</div>
                            </template>
                        </td>
                    </template>
                    <td :class="i.line == 1 ? `font-bold`: ''" x-text="i.ten_dm"></td>
                    <td :class="i.line == 1 ? `font-bold`: ''" x-text="i.ten_bv"></td>
                    <td :class="i.line == 1 ? `font-bold`: ''" x-text="i.gia"></td>
                    <td class="text-center"><input type="checkbox" :name="`kqs[${i.id}][ids][]`" :value="i.kq_id" :checked="_.includes(i.ids, _.toString(i.kq_id))"></td>
                </tr>
            </template>

            </tbody>
        </table>
    </form>

    <div class="flex justify-end my-6">
        <button class="btn-save btn btn-success ">Lưu kết quả</button>
    </div>
@endsection

@push('scripts')
    <script>
        let data = @json($data)

        function kqApp(state = data){
            return {
                data,
                get items(){
                    return _.transform(this.data, function(result, {kqs, ...value}, key) {
                        let l1 = _.get(kqs, '0', {})
                        result.push({
                            ...value,
                            ..._.omit(l1, ['id']), line: '1', count: kqs.length ? kqs.length : 1,
                            kq_id: l1.id,
                            ten_dm_kt: value.ten_dm,
                            index: key
                        })

                        kqs.slice(1).map(i => {
                            result.push({...i, ids: value.ids, id: value.id, kq_id: i.id,line: 'n'})
                        })
                    }, [])
                },

                onChange(e, index){
                    let value = e.target.value
                    this.data[index].loading = true
                    $.post(`/search`, {
                        _token: $('input[name="_token"]').val(),
                        text: value ? value : this.data[index].ten_dm,
                        limit: $('#inp-limit').val() || 10
                    }).then(data => {
                        this.data[index].kqs = data
                        this.data[index].loading = false
                    })

                }
            }
        }

        $(function (){
            $('.btn-save').click(() => {$( "#dm-form" ).submit();})
            $('#inp-limit').change((e) => {
                let limit = e.target.value || 10
                window.location = '/?limit='+limit
            })
        })
    </script>
@endpush
