@extends('layout')

@section('content')
    <div x-data="kqApp()">
        <div class="flex justify-end mb-6">
            <div class="mr-2"><input id="inp-limit" type="text" class="form-control" placeholder="Giới hạn tìm kiếm"
                                     value="{{request('limit')}}"></div>
            <a href="/xoa-kq" class="btn-save btn btn-danger mr-2">Xóa</a>
            <button type="button" class="btn-save btn btn-success" @click="onSubmit">
                <template x-if="loading">
                    <i class="fa fa-solid fa-circle-notch fa-spin mr-2"></i>
                </template>
                Lưu kết quả
            </button>
        </div>
        <form id="dm-form" action="/save-kq" method="POST">
            {{--        <div x-text="JSON.stringify(items)"></div>--}}

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
                        <template x-if="i.line==1">
                            <td class="font-bold" :rowspan="i.count" x-text="i.index+1"></td>
                        </template>
                        <template x-if="i.line==1">
                            <td class="font-bold" :rowspan="i.count" x-text="i.ten_dm_kt"></td>
                        </template>
                        <template x-if="i.line==1">
                            <td :rowspan="i.count">
                                <textarea :name="`kqs[${i.id}][keywords]`" class="form-control inp-keywords" rows="5"
                                          x-text="i.keywords" @change="e => onChange(e, i.index)"></textarea>
                                <template x-if="i.loading">
                                    <div>Loading...</div>
                                </template>
                            </td>
                        </template>
                        <td :class="i.line == 1 ? `font-bold`: ''" x-text="i.ten_dm"></td>
                        <td :class="i.line == 1 ? `font-bold`: ''" x-text="i.ten_bv"></td>
                        <td :class="i.line == 1 ? `font-bold`: ''" x-text="i.gia"></td>
                        <td class="text-center">
                            <template x-if="i.ten_bv">
                                <input type="checkbox" :name="`kqs[${i.id}][ids][]`" :value="i.kq_id"
                                       :checked="_.includes(i.ids, _.toString(i.kq_id))">
                            </template>
                        </td>
                    </tr>
                </template>

                </tbody>
            </table>
        </form>

        <div class="flex justify-end my-6">
            <button type="button" class="btn-save btn btn-success" @click="onSubmit">
                <template x-if="loading">
                    <i class="fa fa-solid fa-circle-notch fa-spin mr-2"></i>
                </template>
                Lưu kết quả
            </button>
        </div>
    </div>

    <div class="toast bg-success position-absolute m-3 top-0 end-0 text-white font-semibold" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Đã lưu thành công
            </div>
            <button type="button" class="btn-close me-2 m-auto text-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let data = @json($data)

        function kqApp(state = data) {
            return {
                data,
                loading: false,
                form: {
                    kqs: data[0].kqs
                },
                get items() {
                    return _.transform(this.data, function (result, {kqs, ...value}, key) {
                        let l1 = _.get(kqs, '0', {})
                        result.push({
                            ...value,
                            ..._.omit(l1, ['id']), line: '1', count: kqs.length ? kqs.length : 1,
                            kq_id: l1.id,
                            ten_dm_kt: value.ten_dm,
                            ten_dm: _.isEmpty(l1) ? '' : value.ten_dm,
                            index: key,
                        })

                        kqs.slice(1).map(i => {
                            result.push({...i, ids: value.ids, id: value.id, kq_id: i.id, line: 'n'})
                        })
                    }, [])
                },

                onChange(e, index) {
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

                },

                onSubmit() {
                    this.loading = true
                    $.post(`/save-kq`, {
                        _token: $('input[name="_token"]').val(),
                        url: $('#dm-form').serialize()
                    }, data => {
                        if(data.status === 'OK') $('.toast').toast('show')
                        this.loading = false
                    })
                }
            }
        }

        $(function () {
            $('#inp-limit').change((e) => {
                let limit = e.target.value || 10
                window.location = '/?limit=' + limit
            })
        })
    </script>
@endpush
