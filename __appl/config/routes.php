<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "home";
$route['404_override'] = '';

$route['registrasi'] = "home/modul/registrasi/";
$route['login'] = "home/modul/login/";
$route['masuk'] = "login";
$route['cekuser'] = "login/cek_user";
$route['cekwaktu'] = "home/get_waktu";
$route['keluar'] = "login/logout";
$route['kelas'] = "home/konten/kelas/";
$route['banksoal/(:any)'] = "home/konten/banksoal/$1";
$route['bab'] = "home/konten/bab/";
$route['sub_bab'] = "home/konten/sub_bab/";
$route['preview'] = "home/konten/prev/";
$route['materi'] = "home/konten/materi/";
$route['konten'] = "home/konten/konten/";
$route['layout'] = "home/konten/layout/";
$route['linksoal/(:num)'] = "home/konten/link/$1";
$route['capcha/(:any)'] = "home/genCaptcha/$1";
$route['getcombo'] = "home/get_combo";
$route['reg'] = "login/simpan_reg";
$route['cekcapcha/(:any)'] = "home/checkCaptcha/$1";
$route['notif'] = "login/register/notif/";
$route['grid/(:any)/(:any)'] = "home/get_grid/$1/$2";
$route['form/(:any)'] = "home/get_form/$1";
$route['upload/(:any)'] = "home/upload/$1";
$route['simpan/(:any)/(:any)'] = "home/simpansavedata/$1/$2";
$route['hapusFile'] = "home/hapus_file";
$route['tree/(:any)'] = "home/tree/$1";
$route['viewvideo'] = "home/konten/video";

/* End of file routes.php */
/* Location: ./application/config/routes.php */