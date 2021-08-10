<?php

use App\Exports\KetquaDmExport;
use App\Models\KetquaDm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

function fullTextWildcards($term)
{
    // removing symbols used by MySQL
    $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
    $term = str_replace($reservedSymbols, '', $term);

    $words = explode(' ', $term);

    foreach ($words as $key => $word) {
        /*
         * applying + operator (required word) only big words
         * because smaller ones are not indexed by mysql
         */
        if (strlen($word) >= 2) {
            $words[$key] = '+' . $word . '*';
        }
    }

    $searchTerm = implode(' ', $words);

    return $searchTerm;
}

Route::get('/', function (\Illuminate\Http\Request $request) {
//    DB::statement("UPDATE danhmuc SET gia = REPLACE(gia,',000000','')");
    $limit = $request->input('limit', 10);

    $kqs = collect(KetquaDm::all()->toArray())->map(fn($i) => (object)$i);
    foreach ($kqs as $k => $i){
//        $keywords = Str::of($i->ten_dm)->slug('* +', false)->prepend('+')->append('*');

        $r = DB::table('danhmuc');
        if($i->keywords) {
            $r->whereRaw("MATCH (ten_dm) AGAINST ('{$i->keywords}' IN BOOLEAN MODE)");
        } else {
            $keywords = $i->ten_dm;
            $r->whereRaw("MATCH (ten_dm) AGAINST ('{$keywords}' IN NATURAL LANGUAGE MODE)");
        }

        $r = $r->take($limit)->get();
        $kqs[$k]->kqs = $r;
    }

    return view('danhmuc', ['data' => $kqs]);
});


Route::post('save-kq', function (\Illuminate\Http\Request $request){
    parse_str($_POST['url'], $data);

    KetquaDm::whereNotIn('id', array_keys($data['kqs']))->update(['ids' => null]);

    foreach ($data['kqs'] as $id => $v){
        $model = KetquaDm::find($id);
        $model->fill($v);
        $model->save();
    }

    return ['status' => 'OK'];
//    return redirect('/ketqua');
});

Route::any('ketqua', function (\Illuminate\Http\Request $request){
    $limit = $request->input('limit', 3);

    $data = DB::select('SELECT kq.*, dm.ten_dm dm, ten_bv, gia FROm ketqua_dm kq LEFT JOIN danhmuc dm ON JSON_CONTAINS(kq.ids, CONCAT(\'"\', dm.id,\'"\')) ORDER BY id');
    $data = collect($data)->groupBy('id')->map(function ($i) use($limit){
        $value = [
            'id' => data_get($i, '0.id'),
            'ten_dm' => data_get($i, '0.ten_dm'),
        ];

        $i->take($limit)->map(function ($j, $k) use(&$value){
            $value['ten_bv_'.($k+1)] = $j->ten_bv;
            $value['gia_'.($k+1)] = $j->gia;
        });
        return $value;
    })->values();

    if($request->has('export')){
        $export = new KetquaDmExport($data->all());
        return Excel::download($export, 'ketqua_dm.xlsx');
    }

    return view('ketqua', compact('data'));
});

Route::get('/xoa-kq', function (){
    KetquaDm::whereRaw('1=1')->update(['ids' => null]);

    return redirect('/');
});

Route::post('/search', function (\Illuminate\Http\Request $request){
    $limit = $request->input('limit', 10);
    $keywords = $request->text;
//    $keywords = Str::of($request->text)->slug('* +', false)->prepend('+')->append('*');

    $r = DB::table('danhmuc');
    $r->whereRaw("MATCH (ten_dm) AGAINST ('{$keywords}' IN BOOLEAN MODE)");

    $r = $r->take($limit)->get();

    return $r;
});
