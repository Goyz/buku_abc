var index_row;

function loadUrl(obj, urls){
    $("#isi_na").html("").addClass("loading");
	$.get(urls,function (html){
	    $("#isi_na").html(html).removeClass("loading");
    });
}

function getClientHeight(){
	var theHeight;
	if (window.innerHeight)
		theHeight=window.innerHeight;
	else if (document.documentElement && document.documentElement.clientHeight) 
		theHeight=document.documentElement.clientHeight;
	else if (document.body) 
		theHeight=document.body.clientHeight;
	
	return theHeight;
}

var divcontainer;
function windowFormPanel(html,judul,width,height){
	divcontainer = $('#jendela');
	$(divcontainer).unbind();
	$('#isiJendela').html(html);
    $(divcontainer).window({
		title:judul,
		width:width,
		height:height,
		autoOpen:false,
		top: Math.round(frmHeight/2)-(height/2),
		left: Math.round(frmWidth/2)-(width/2),
		modal:true,
		maximizable:false,
		minimizable: false,
		collapsible: false,
		closable: true,
		resizable: false,
	    onBeforeClose:function(){	   
			$(divcontainer).window("close",true);
			//$(divcontainer).window("destroy",true);
			//$(divcontainer).window('refresh');
			return true;
	    }		
    });
    $(divcontainer).window('open');       
}
function windowFormClosePanel(){
    $(divcontainer).window('close');
	//$(divcontainer).window('refresh');
}

var container;
function windowForm(html,judul,width,height){
    container = "win"+Math.floor(Math.random()*9999);
    $("<div id="+container+"></div>").appendTo("body");
    container = "#"+container;
    $(container).html(html);
    $(container).css('padding','5px');
    $(container).window({
       title:judul,
       width:width,
       height:height,
       autoOpen:false,
       maximizable:false,
       minimizable: false,
	   collapsible: false,
       resizable: false,
       closable:true,
       modal:true,
	   onBeforeClose:function(){	   
			$(container).window("close",true);
			$(container).window("destroy",true);
			return true;
	   }
    });
    $(container).window('open');        
}
function closeWindow(){
    $(container).window('close');
    $(container).html("");
}


function getClientWidth(){
	var theWidth;
	if (window.innerWidth) 
		theWidth=window.innerWidth;
	else if (document.documentElement && document.documentElement.clientWidth) 
		theWidth=document.documentElement.clientWidth;
	else if (document.body) 
		theWidth=document.body.clientWidth;

	return theWidth;
}



function genGrid(modnya, divnya, lebarnya, tingginya){
	if(lebarnya == undefined){
		lebarnya = getClientWidth-250;
	}
	if(tingginya == undefined){
		tingginya = getClientHeight-300
	}

	var kolom ={};
	var frozen ={};
	var judulnya;
	var param={};
	var urlnya;
	var urlglobal="";
	var url_detil="";
	var post_detil={};
	var fitnya;
	var klik=false;
	var doble_klik=false;
	var pagesizeboy = 10;
	var singleSelek = true;
	var footer=false;
	var row_number=true;
	var nowrap=true;
	switch(modnya){
		
		case "mata_pelajaran":
			judulnya = "Daftar Mata PElajaran";
			urlnya = "cl_mata_pelajaran";
			fitnya = true;
			footer=true;
			url_detil=host+"form/mata_pelajaran";
			//param['bulan']=$('#bulan_main').val();
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'nama_pelajaran',title:'Pelajaran',width:350, halign:'center',align:'left'},
				{field:'kelas',title:'Kelas',width:150, halign:'center',align:'left'},
				{field:'create_date',title:'Dibuat Tgl',width:200, halign:'center',align:'center'},
				{field:'create_by',title:'Dibuat Oleh',width:200, halign:'center',align:'left'}
				
			];
		break;
		case "bab":
			judulnya = "Daftar Bab Dalam Pelajaran";
			urlnya = "tbl_bab_pelajaran";
			fitnya = true;
			footer=true;
			url_detil=host+"form/bab_pelajaran";
			//param['bulan']=$('#bulan_main').val();
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'nama_pelajaran',title:'Pelajaran',width:300, halign:'center',align:'left'},
				{field:'bab',title:'BAB',width:50, halign:'center',align:'left'},
				{field:'judul_bab',title:'Judul Bab',width:250, halign:'center',align:'left'},
				{field:'deskripsi_bab',title:'Desc. Bab',width:250, halign:'center',align:'left'},
				{field:'create_date',title:'Dibuat Tgl',width:150, halign:'center',align:'center'},
				{field:'create_by',title:'Dibuat Oleh',width:150, halign:'center',align:'left'}
				
			];
		break;
		case "sub_bab":
			judulnya = "Daftar SubBab Dalam Pelajaran";
			urlnya = "tbl_sub_bab_pelajaran";
			fitnya = true;
			footer=true;
			url_detil=host+"form/sub_bab_pelajaran";
			//param['bulan']=$('#bulan_main').val();
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'judul_bab',title:'BAB',width:250, halign:'center',align:'left'},
				{field:'sub_bab',title:'Sub Bab',width:80, halign:'center',align:'left'},
				{field:'judul_sub_bab',title:'Judul SubBab',width:250, halign:'center',align:'left'},
				{field:'deskripsi_sub_bab',title:'Desc. SubBab',width:250, halign:'center',align:'left'},
				{field:'create_date',title:'Dibuat Tgl',width:150, halign:'center',align:'center'},
				{field:'create_by',title:'Dibuat Oleh',width:150, halign:'center',align:'left'}
				
			];
		break;
		case "materi":
			judulnya = "Daftar SubBab Dalam Pelajaran";
			urlnya = "tbl_materi";
			fitnya = true;
			footer=true;
			url_detil=host+"form/materi";
			//param['bulan']=$('#bulan_main').val();
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'judul_sub_bab',title:'SubBab',width:250, halign:'center',align:'left'},
				{field:'nama_materi',title:'Nama Materi',width:300, halign:'center',align:'left'},
				{field:'deskripsi_materi',title:'Desc. Materi',width:250, halign:'center',align:'left'},
				{field:'create_date',title:'Dibuat Tgl',width:150, halign:'center',align:'center'},
				{field:'create_by',title:'Dibuat Oleh',width:150, halign:'center',align:'left'}
				
			];
		break;
		case "konten":
			judulnya = "Daftar Konten Materi ";
			urlnya = "tbl_konten_materi";
			fitnya = true;
			footer=true;
			url_detil=host+"form/konten_materi";
			//param['bulan']=$('#bulan_main').val();
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'judul_sub_bab',title:'SubBab',width:300, halign:'center',align:'left'},
				{field:'isi_konten',title:'Konten',width:450, halign:'center',align:'left'},
				{field:'create_date',title:'Dibuat Tgl',width:150, halign:'center',align:'center'},
				{field:'create_by',title:'Dibuat Oleh',width:150, halign:'center',align:'left'}
				
			];
		break;
		case "un":
		case "smbptn":
		case "latihan":
		case "banksoal":
			judulnya = "Daftar Bank Soal "+modnya.toUpperCase();
			urlnya = "tbl_bank_soal";
			fitnya = true;
			footer=true;
			url_detil=host+"form/konten_materi";
			param['cat']=modnya;
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'tahun',title:'Tahun',width:100, halign:'center',align:'center'},
				{field:'judul_sub_bab',title:'SubBab',width:300, halign:'center',align:'left'},
				{field:'isi_soal',title:'Konten',width:450, halign:'center',align:'left'},
				{field:'create_date',title:'Dibuat Tgl',width:150, halign:'center',align:'center'},
				{field:'create_by',title:'Dibuat Oleh',width:150, halign:'center',align:'left'}
				
			];
		break;
		case "kelas":
			judulnya = "Daftar Kelas";
			urlnya = "cl_kelas";
			fitnya = true;
			footer=true;
			url_detil=host+"form/kelas";
			//param['bulan']=$('#bulan_main').val();
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'kelas',title:'Kelas',width:250, halign:'center',align:'left'},
				{field:'create_date',title:'Dibuat Tgl',width:220, halign:'center',align:'center'},
				{field:'create_by',title:'Dibuat Oleh',width:200, halign:'center',align:'left'}
				
			];
		break;
		case "peserta":
			judulnya = "Daftar Peserta ";
			urlnya = "cl_peserta";
			fitnya = true;
			footer=true;
			row_number=false;
			url_detil=host+"form/kelas";
			//param['bulan']=$('#bulan_main').val();
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'sts',title:'Status',width:200, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						if(value==1){
							return 'User Aktif';
						}
						else{
							return 'User Tidak Aktif';
						}
					},
					styler:function(value,rowData,rowIndex){
						if(value!=1){return 'background:red;'}
					}
				},
				{field:'Nama',title:'Nama',width:250, halign:'center',align:'left'},
				{field:'kelas',title:'Kelas',width:150, halign:'center',align:'left'},
				{field:'Asal_sekolah',title:'Sekolah',width:150, halign:'center',align:'left'},
				{field:'TempatLahir',title:'Tempat, Tgl. Lahir',width:250, halign:'center',align:'left',
					formatter:function(value,rowData,rowIndex){
							return value+', '+rowData.Tgl_Lahir;
					}
				},
				{field:'Alamat',title:'Alamat',width:350, halign:'center',align:'left'},
				{field:'reg_date',title:'Register Tgl',width:150, halign:'center',align:'center'}
				
			];
		break;
		case "guru":
			judulnya = "Daftar Guru/Pengajar ";
			urlnya = "cl_guru";
			fitnya = true;
			footer=true;
			row_number=false;
			nowrap=false;
			url_detil=host+"form/guru";
			//param['bulan']=$('#bulan_main').val();
			//param['tahun']=$('#tahun_main').val();
			kolom[modnya] = [	
				{field:'sts',title:'Status',width:200, halign:'center',align:'center',
					formatter:function(value,rowData,rowIndex){
						if(value==1){
							return 'User Aktif';
						}
						else{
							return 'User Tidak Aktif';
						}
					},
					styler:function(value,rowData,rowIndex){
						if(value!=1){return 'background:red;'}
					}
				},
				{field:'nama_lengkap',title:'Nama',width:250, halign:'center',align:'left'},
				{field:'alamat',title:'Alamat',width:250, halign:'center',align:'left'},
				{field:'jenis_kelamin',title:'Jenis Kelamin',width:150, halign:'center',align:'left',
					formatter:function(value,rowData,rowIndex){
						if(value==1){return "Laki-laki";}
						else{return "Wanita";}
					}
				},
				{field:'email',title:'Email',width:150, halign:'center',align:'left'},
				{field:'telp',title:'Telp. Rumah',width:150, halign:'center',align:'left'},
				{field:'hp',title:'No. HP',width:150, halign:'center',align:'left'},
				{field:'reg_date',title:'Register Tgl',width:150, halign:'center',align:'center'}
				
			];
		break;
		//End Cost Object
	}
	
	grid_nya=$("#"+divnya).datagrid({
		title:judulnya,
        height:tingginya,
        width:lebarnya,
		rownumbers:row_number,
		iconCls:'database',
        fit:fitnya,
        striped:true,
        pagination:true,
        remoteSort: false,
		showFooter:footer,
		singleSelect:singleSelek,
        url: (urlglobal == "" ? host+"home/getdata/"+urlnya : urlglobal),		
		nowrap: nowrap,
		pageSize:pagesizeboy,
		pageList:[10,20,30,40,50,75,100,200],
		queryParams:param,
		frozenColumns:[
            frozen[modnya]
        ],
		columns:[
            kolom[modnya]
        ],
		onLoadSuccess:function(d){
			//console.log(d.total);
			if(modnya=='list_act'){
				if(d.key=='off'){
					if(d.total==0){
						$.messager.confirm('ABC System','No Data In This Period, Do You Want To Get Data In First Period ?',function(r){
							if(r){
								loadingna();
								$.post(host+'home/copy_act',{bulan:$('#bulan_main').val(),tahun:$('#tahun_main').val()},function(resp){
									if(resp==1){
										$.messager.alert('ABC System', "Data Was Copied", 'info');
										winLoadingClose();
										grid_nya.datagrid('reload');
									}
									else if(resp==2){
										$.messager.alert('ABC System', "No Data In Last Period, Please Upload Or Insert Data Activity", 'error');
										winLoadingClose();
										console.log(resp);
									}
									else{
										$.messager.alert('ABC System', "No Data Activity In New Period", 'error');
										winLoadingClose();
										console.log(resp);
									}
								});
							}
						});
					}
				}
			}
			//gridVRList.datagrid('selectRow', 0);
			$('.yes').linkbutton({  
					iconCls: 'icon-cancel'  
			});
			$('.no').linkbutton({  
					iconCls: 'icon-ok'  
			});
			
		},
		onClickRow:function(rowIndex,rowData){
		  if(klik==true){
			  if(modnya=='list_act'){
				  $('#id').val(rowData.id);
				  $('#activity_code').val(rowData.activity_code);
				  $('#descript').val(rowData.descript);
				  $('#editstatus').val('edit');
			  }
		  }
        },
		onDblClickRow:function(rowIndex,rowData){
			if(doble_klik==true){
				switch(modnya){
					case "list_act":
						post_detil['id']=rowData.id;
						$('#daftar_grid').hide();
						$('#detil_grid').show();
						$('#detil_grid').addClass('loading').html('');
						$.post(url_detil,post_detil,function(r){
							$('#detil_grid').removeClass('loading').html(r);
						});
					break;
					case "employees":
						post_detil['id'] = rowData.id;
						$('#grid_nya_'+modnya).addClass('loading').html('');
						$.post(url_detil,post_detil,function(r){
							$('#grid_nya_'+modnya).removeClass('loading').html(r);
						});
					break;
					
				}
			}
		},
		toolbar: '#tb_'+modnya,
	});
}


function genform(type, modulnya, submodulnya, stswindow, tabel){
	var urlpost = host+'form/'+modulnya;
	var urldelete = host+'home/simpansavedata/'+tabel;
	var id_tambahan = "";
	
	switch(modulnya){
		case "201":
			var lebar = getClientWidth()-990;
			var tinggi = getClientHeight()-535;
			var judulwindow = 'Form Data Provinsi';
			var table="cl_provinsi";
		break;
				
		//End tabs Location - Cost Object
	}
	
	switch(type){
		
		case "add":
			if(stswindow == undefined){
				$('#grid_nya_'+modulnya).hide();
				$('#detil_nya_'+modulnya).html('').show().addClass("loading");
			}
			$.post(urlpost, {'editstatus':'add', 'id_tambahan':id_tambahan }, function(resp){
				if(stswindow == 'windowform'){
					windowForm(resp, judulwindow, lebar, tinggi);
				}else if(stswindow == 'windowpanel'){
					windowFormPanel(resp, judulwindow, lebar, tinggi);
				}else{
					$('#detil_nya_'+modulnya).show();
					$('#detil_nya_'+modulnya).html(resp).removeClass("loading");
				}
			});
		break;
		case "edit":
		case "delete":
		case "copy_model":
			var row = $("#grid_"+modulnya).datagrid('getSelected');
			if(row){
				if(type=='edit'){
					if(stswindow == undefined){
						$('#grid_nya_'+modulnya).hide();
						$('#detil_nya_'+modulnya).html('').show().addClass("loading");	
					}
					$.post(urlpost, { 'editstatus':'edit', id:row.id, 'tabel':table, 'modul':modulnya, 'bulan':row.bulan, 'tahun':row.tahun, 'id_tambahan':id_tambahan }, function(resp){
						if(stswindow == 'windowform'){
							windowForm(resp, judulwindow, lebar, tinggi);
						}else if(stswindow == 'windowpanel'){
							windowFormPanel(resp, judulwindow, lebar, tinggi);
						}else{
							$('#detil_nya_'+modulnya).show();
							$('#detil_nya_'+modulnya).html(resp).removeClass("loading");
						}
					});
				}
				else if(type=='delete'){
					if(confirm("Do You Want To Delete This Data ?")){
						loadingna();
						$.post(urldelete, {id:row.id,'editstatus':'delete'}, function(r){
							if(r==1){
								winLoadingClose();
								$.messager.alert('Bimbel',"Data Sudah Terhapus",'info');
								$('#grid_'+modulnya).datagrid('reload');
								
								var arraynya = [
									'assign_act_employee',
									'expense_source_employee',
									'assign_act_expense',
									'assign_emp_expense',
									'assign_assets_expense',
									'assign_act_assets',
									'assign_exp_assets',
								];
								if( $.inArray(modulnya, arraynya) > -1 ){
									$.post(urltot, {}, function(respo){
										var obj = $.parseJSON(respo);
										$('#'+divtotcost).html(obj.total_cost);
										$('#'+divtotpercent).val(obj.total_percent);
										$('#'+divtxtpercent).val(obj.total_percent);
									});
								}
								
							}
							else{
								winLoadingClose();
								console.log(r)
								$.messager.alert('Bimbel',"Failed",'error');
							}
						});	
					}
				}
				else if(type=='copy_model'){
					$.post(host+'home/modul/'+modulnya+'/copy_model', { id:row.id }, function(resp){
						windowForm(resp, judulwindow, (getClientWidth()-500), (getClientHeight()-280));
					});
				}	
			}
			else{
				$.messager.alert('Bimbel',"Pilih Data Dalam List",'error');
			}
		break;
	}
}

function submit_form(frm,func){
	var url = jQuery('#'+frm).attr("url");
    jQuery('#'+frm).form('submit',{
            url:url,
            onSubmit: function(){
                  return $(this).form('validate');
            },
            success:function(data){
				//$.unblockUI();
                if (func == undefined ){
                     if (data == "1"){
                        pesan('Data Sudah Disimpan ','Sukses');
                    }else{
                         pesan(data,'Result');
                    }
                }else{
                    func(data);
                }
            },
            error:function(data){
				//$.unblockUI();
                 if (func == undefined ){
                     pesan(data,'Error');
                }else{
                    func(data);
                }
            }
    });
}
function genTab(div,mod,sub_mod,tab_array,div_panel,judul_panel,mod_num, height_panel, height_tab,width_panel,width_tab){
	var id_sub_mod=sub_mod.split("_");
	if(typeof(div_panel)!= "undefined" || div_panel!=""){
	$(div_panel).panel({
		width:(typeof(width_panel) == "undefined" ? getClientWidth()-268 : width_panel),
		height:(typeof(height_panel) == "undefined" ? getClientHeight()-100 : height_panel),
		title:judul_panel,
		//fit:true,
		tools:[{
				iconCls:'icon-cancel',
				handler:function(){
					$('#grid_nya_'+id_sub_mod[1]).show();
					$('#detil_nya_'+id_sub_mod[1]).hide();
					$('#grid_'+id_sub_mod[1]).datagrid('reload');
				}
		}]
	}); 
	}
	$(div).tabs({
		title:'AA',
		//height: getClientHeight()-190,
		height: (typeof(height_tab) == "undefined" ? getClientHeight()-190 : height_tab),
		width: (typeof(width_tab) == "undefined" ? getClientWidth()-280 : width_tab),
		plain: false,
		//fit:true,
		selected:0
	});
	
	if(tab_array.length > 0){
		for(var x in tab_array){
			var isi_tab=tab_array[x].replace(/ /g,"_");
			$(div).tabs('add',{
					title:tab_array[x],
					iconCls:'database',
					content:'<div style="padding: 5px;"><div id="'+isi_tab.toLowerCase()+'" style="height: 200px;">'+isi_tab.toLowerCase()+'</div></div>'
			});
		}
		$(div).tabs({
			onSelect: function(title){
				var isi_tab=title.replace(/ /g,"_");
				var par={};
				$('#'+isi_tab.toLowerCase()).html('').addClass('loading');
				urlnya = host+'home/modul/'+mod+'/'+isi_tab.toLowerCase();
				$(div_panel).panel({title:title});
				
				switch(mod){
					case "activity_master":
						par['par_1']=$('#par_1').val();
						par['par_2']=$('#par_2').val();
						par['par_3']=$('#par_3').val();
					break;
					case "model":
						par['par_1']=$('#id_activity').val();
					break;
					case "activity":
						if(typeof(id_act) != "undefined")par['id_act']=id_act;
						par['bulan']=$('#bulan_main').val();
						par['tahun']=$('#tahun_main').val();
					break;
					case "process_master":
						par['par_1']=$('#par_1').val();
						par['par_2']=$('#par_2').val();
					break;
					
					case "resource":
						urlnya = host+'homex/modul/'+mod+'/'+isi_tab.toLowerCase();
					break;
					case "cost_object":
						urlnya = host+'homex/modul/'+mod+'/'+isi_tab.toLowerCase();
					break;
					case "report":
						switch(isi_tab.toLowerCase()){
							case "activity_master":
							case "activity_driver":
							case "activity_by_segment":
							case "activity_resource":
								main_report=isi_tab.toLowerCase();
								par['bulan']=$('#bulan_activity').val();
								par['tahun']=$('#tahun_activity').val();
								urlnya = host+'home/modul/'+mod+'/'+isi_tab.toLowerCase();
							break;
							case "resource_employee":
							case "resource_expense":
							case "resource_assets":
								main_report=isi_tab.toLowerCase();
								urlnya = host+'homex/modul/'+mod+'/'+isi_tab.toLowerCase();
							break;
							case "costobject_cost":
							case "costobject_customer":
							case "costobject_location":
								main_report=isi_tab.toLowerCase();
								urlnya = host+'homex/modul/'+mod+'/'+isi_tab.toLowerCase();
							break;
							default:
								urlnya = host+'homex/modul/'+mod+'/'+isi_tab.toLowerCase();
							break;
						}
					break;
					case "parameter":
						urlnya = host+'homex/modul/'+mod+'/'+isi_tab.toLowerCase();
					break;
					//default:urlnya = host+'home/modul/'+mod+'/'+isi_tab.toLowerCase();
				}
				console.log(par);
					$.post(urlnya,par,function(r){
						//$('#'+isi_tab).html(isi_tab+' -> '+title);
						$('#'+isi_tab.toLowerCase()).removeClass('loading').html(r);
						//$('#'+isi_tab.toLowerCase()).html(r);
					});
				//console.log(title);
			}
		});
		var tab = $(div).tabs('select',0);
		
	}
	
}

function cariData(typecari){
	var costcenter = $('#cost_center_'+typecari).val();
	var month = $('#bulan_'+typecari).val();
	var year = $('#tahun_'+typecari).val();
	var post_search = {};
	
	post_search['cost_center'] = costcenter;
	post_search['month'] = month;
	post_search['year'] = year;
	
	$('#grid_'+typecari).datagrid('reload', post_search);
}

function fillCombo(url, SelID, value, value2, value3, value4){
	//if(Ext.get(SelID).innerHTML == "") return false;
	if (value == undefined) value = "";
	if (value2 == undefined) value2 = "";
	if (value3 == undefined) value3 = "";
	if (value4 == undefined) value4 = "";
	
	$('#'+SelID).empty();
	$.post(url, {"v": value, "v2": value2, "v3": value3, "v4": value4},function(data){
		$('#'+SelID).append(data);
	});

}
function formatDate(date) {
	var bulan=date.getMonth() +1;
	var tgl=date.getDate();
	if(bulan < 10){
		bulan='0'+bulan;
	}
	
	if(tgl < 10){
		tgl='0'+tgl;
	}
	return date.getFullYear() + "-" + bulan + "-" + tgl;
}

function kumpulAction(type, p1, p2, p3){
	var post_detil = {};
	
	switch(type){
		case 'changemodul':
			var param = $('#modul_reference').val();
			if(param == ""){
				$('#template').html("");
			}else{
				var textnya = $('#modul_reference option:selected').text();
				var htmlnya = "<a style='text-decoration:none;' href='"+host+"homex/download/"+param+"' target='_blank' >Template "+textnya+"</a>";
				$('#template').html(htmlnya);
			}
		break;
		case "userrole":
			$.post(host+'homex/modul/setting/form_user_role', {'id':p1, 'editstatus':'add'}, function(resp){
				var lebar = getClientWidth()-500;
				var tinggi = getClientHeight()-200;
				windowForm(resp, "User Group Role Privilleges", lebar, tinggi);
			});
		break;
		case "form_data_production":
			
			var row = $("#master_prm").datagrid('getSelected');
			if(row){
				$('#'+p1).html('');
				$.post(host+'homex/modul/production/form_data_production', { 'bulan':p2 , 'tahun':p3 ,'deskripsi':row.descript, 'prod_id':row.prod_id, 'tbl_prm_id':row.id },function(resp){
					$('#'+p1).html(resp);
				});
			}else{
				$.messager.alert('ABC System',"Select Row In Grid",'error');
			}
		break;
		case 'removeproduction':
			var row = $("#tabel_prd").edatagrid('getSelected');
			if(row){
				$.post(host+'homex/simpansavedata/tbl_prd/', { 'editstatus':'delete', 'id':row.id },function(resp){
					$("#tabel_prd").edatagrid('reload');
					$("#master_cdm").datagrid('reload');
				});
			}else{
				$.messager.alert('ABC System',"Select Row In Grid Data Production",'error');
			}
		break;
		
		//Assignment Data : Resource
		case "list_activity_employee":
		case "list_expense_employee":		
			var row = $("#grid_"+type).edatagrid('getSelections');
			if(row){
				var assignment = $('#jenis_assignment').val();
				post_detil['tbl_emp_id'] = $('#hdn_'+type).val();
				post_detil['editstatus'] = 'kontel';
				post_detil['datanya'] = row;
				post_detil['id'] = 'kontel';
				
				if(assignment == 'list_activity_employee' || assignment == 'list_expense_employee'){
					post_detil['bulan']	= $('#bulan_employee').val();
					post_detil['tahun']	= $('#tahun_employee').val();
				}
				
				$.post(host+'homex/simpansavedata/'+type, post_detil, function(r){
					if(r == 1){
						$.messager.alert('ABC System',"Data Saved",'info');
					}else{
						$.messager.alert('ABC System', "Failed Saved -"+r, 'error');
					}
					closeWindow();
					
					if(type == 'list_activity_employee'){
						$('#grid_assign_act_employee').datagrid('reload');
					}else if(type == 'list_expense_employee'){
						$('#grid_expense_source_employee').datagrid('reload');
					}
				});
				
			}else{
				$.messager.alert('ABC System',"Select Row In Grid Data",'error');
			}
		break;
		
		case 'list_activity_expense':
		case 'list_employee_expense':
		case 'list_assets_expense':
			var row = $("#grid_"+type).edatagrid('getSelections');
			if(row){
				var assignment = $('#jenis_assignment').val();
				post_detil['tbl_exp_id'] = $('#hdn_'+type).val();
				post_detil['editstatus'] = 'kontel';
				post_detil['datanya'] = row;
				post_detil['id'] = 'kontel';
				post_detil['bulan']	= $('#bulan_expense').val();
				post_detil['tahun']	= $('#tahun_expense').val();
				
				
				$.post(host+'homex/simpansavedata/'+type, post_detil, function(r){
					if(r == 1){
						$.messager.alert('ABC System',"Data Saved",'info');
					}else{
						$.messager.alert('ABC System', "Failed Saved -"+r, 'error');
					}
					closeWindow();
					
					if(type == 'list_activity_expense'){
						$('#grid_assign_act_expense').datagrid('reload');
					}else if(type == 'list_employee_expense'){
						$('#grid_assign_emp_expense').datagrid('reload');
					}else if(type == 'list_assets_expense'){
						$('#grid_assign_assets_expense').datagrid('reload');
					}
				});
				
			}else{
				$.messager.alert('ABC System',"Select Row In Grid Data",'error');
			}
		break;
		
		case 'list_activity_assets':
		case 'list_expense_assets':
			var row = $("#grid_"+type).edatagrid('getSelections');
			if(row){
				var assignment = $('#jenis_assignment').val();
				post_detil['tbl_assets_id'] = $('#hdn_'+type).val();
				post_detil['editstatus'] = 'kontel';
				post_detil['datanya'] = row;
				post_detil['id'] = 'kontel';
				post_detil['bulan']	= $('#bulan_assets').val();
				post_detil['tahun']	= $('#tahun_assets').val();
				
				$.post(host+'homex/simpansavedata/'+type, post_detil, function(r){
					if(r == 1){
						$.messager.alert('ABC System',"Data Saved",'info');
					}else{
						$.messager.alert('ABC System', "Failed Saved -"+r, 'error');
					}
					closeWindow();
					
					if(type == 'list_activity_assets'){
						$('#grid_assign_act_assets').datagrid('reload');
					}else if(type == 'list_expense_assets'){
						$('#grid_assign_exp_assets').datagrid('reload');
					}
				});
				
			}else{
				$.messager.alert('ABC System',"Select Row In Grid Data",'error');
			}
		break;
		//End Assignment
		
		// Modul Cost Object
		case 'list_activity_costobject':
		case 'list_customer_costobject':
		case 'list_location_costobject':
			var row = $("#grid_"+type).edatagrid('getSelections');
			if(row){
				var assignment = $('#jenis_assignment').val();
				post_detil['tbl_prm_id'] = $('#hdn_'+type).val();
				post_detil['editstatus'] = 'kontel';
				post_detil['datanya'] = row;
				post_detil['id'] = 'kontel';
				post_detil['bulan']	= $('#bulan_cost_object').val();
				post_detil['tahun']	= $('#tahun_cost_object').val();
				
				$.post(host+'homex/simpansavedata/'+type, post_detil, function(r){
					if(r == 1){
						$.messager.alert('ABC System',"Data Saved",'info');
					}else{
						$.messager.alert('ABC System', "Failed Saved -"+r, 'error');
					}
					closeWindow();
					
					if(type == 'list_activity_costobject'){
						$('#grid_assign_act_costobject').datagrid('reload');
					}else if(type == 'list_customer_costobject'){
						$('#grid_assign_cust_costobject').datagrid('reload');
					}else if(type == 'list_location_costobject'){
						$('#grid_assign_loc_costobject').datagrid('reload');
					}
				});
				
			}else{
				$.messager.alert('ABC System',"Select Row In Grid Data",'error');
			}
		break;
		
		case 'list_costobject_customer':
		case 'list_location_customer':
			var row = $("#grid_"+type).edatagrid('getSelections');
			if(row){
				var assignment = $('#jenis_assignment').val();
				post_detil['tbl_cust_id'] = $('#hdn_'+type).val();
				post_detil['editstatus'] = 'kontel';
				post_detil['datanya'] = row;
				post_detil['id'] = 'kontel';
				post_detil['bulan']	= $('#bulan_customer').val();
				post_detil['tahun']	= $('#tahun_customer').val();
				
				$.post(host+'homex/simpansavedata/'+type, post_detil, function(r){
					if(r == 1){
						$.messager.alert('ABC System',"Data Saved",'info');
					}else{
						$.messager.alert('ABC System', "Failed Saved -"+r, 'error');
					}
					closeWindow();
					
					if(type == 'list_costobject_customer'){
						$('#grid_assign_costobject_cust').datagrid('reload');
					}else if(type == 'list_location_customer'){
						$('#grid_assign_location_cust').datagrid('reload');
					}
				});
				
			}else{
				$.messager.alert('ABC System',"Select Row In Grid Data",'error');
			}
		break;
		
		case 'list_costobject_location':
		case 'list_customer_location':
			var row = $("#grid_"+type).edatagrid('getSelections');
			if(row){
				var assignment = $('#jenis_assignment').val();
				post_detil['tbl_location_id'] = $('#hdn_'+type).val();
				post_detil['editstatus'] = 'kontel';
				post_detil['datanya'] = row;
				post_detil['id'] = 'kontel';
				post_detil['bulan']	= $('#bulan_location').val();
				post_detil['tahun']	= $('#tahun_location').val();
				
				$.post(host+'homex/simpansavedata/'+type, post_detil, function(r){
					if(r == 1){
						$.messager.alert('ABC System',"Data Saved",'info');
					}else{
						$.messager.alert('ABC System', "Failed Saved -"+r, 'error');
					}
					closeWindow();
					
					if(type == 'list_costobject_location'){
						$('#grid_assign_costobject_location').datagrid('reload');
					}else if(type == 'list_customer_location'){
						$('#grid_assign_cust_location').datagrid('reload');
					}
				});
				
			}else{
				$.messager.alert('ABC System',"Select Row In Grid Data",'error');
			}
		break;
		
		// End Modul Cost Object
		
		//Modul Report
		case "profit":
			post_detil['bulan']	= $('#bulan_profit').val();
			post_detil['tahun']	= $('#tahun_profit').val();
			
			$('#main-report').html('').addClass("loading");
			$.post(host+'homex/modul/report/profit_detail', post_detil, function(r){
				$('#main-report').html(r).removeClass("loading");
			});
		break;
		//End Modul Report
	}
}		

function clear_form(id){
	$('#'+id).find("input[type=text], textarea,select").val("");
	//$('.angka').numberbox('setValue',0);
}

var divcontainerz;
function windowLoading(html,judul,width,height){
    divcontainerz = "win"+Math.floor(Math.random()*9999);
    $("<div id="+divcontainerz+"></div>").appendTo("body");
    divcontainerz = "#"+divcontainerz;
    $(divcontainerz).html(html);
    $(divcontainerz).css('padding','5px');
    $(divcontainerz).window({
       title:judul,
       width:width,
       height:height,
       autoOpen:false,
       modal:true,
       maximizable:false,
       resizable:false,
       minimizable:false,
       closable:false,
       collapsible:false,  
    });
    $(divcontainerz).window('open');        
}
function winLoadingClose(){
    $(divcontainerz).window('close');
    //$(divcontainer).html('');
}
function loadingna(){
	windowLoading("<img src='"+host+"__assets/images/loading.gif' style='position: fixed;top: 50%;left: 50%;margin-top: -10px;margin-left: -25px;'/>","Please Wait",200,100);
}

function transfer_data(from,to,grid_id_from,grid_id_to, grid_id_destination,flag_oke){
	//var row=$('#'+grid_id_from).datagrid('getSelected');
	console.log(grid_id_from);
	var row=$('#'+grid_id_from).datagrid('getSelections');
	var post={};
	var id_grid=[];
		
		if(row.length > 0){
			//console.log(row_emp.id);
			loadingna();
			for(x in row){
				id_grid.push(row[x].id);
			}
				switch(to){
					case "tbl_emp_act":
						post['editstatus']='add';
						post['tbl_emp_id']=id_grid;
					break;
					case "tbl_asset_are":
						post['editstatus']='add';
						post['tbl_acm_id']=id_act;
						post['bulan']=$('#bulan_main').val();
						post['tahun']=$('#tahun_main').val();
						post['tbl_assets_id']=id_grid;
					break;
					case "tbl_are_asset":
						to="tbl_are";
						post['editstatus']='delete';
						post['id']=id_grid;
					break;
					case "tbl_are":
						post['editstatus']='add';
						post['tbl_emp_id']=id_grid;
						post['tbl_acm_id']=id_act;
						post['bulan']=$('#bulan_main').val();
						post['tahun']=$('#tahun_main').val();
					break;
					case "tbl_are_exp":
						post['editstatus']='add';
						post['tbl_exp_id']=id_grid;
						post['tbl_acm_id']=id_act;
						post['bulan']=$('#bulan_main').val();
						post['tahun']=$('#tahun_main').val();
					break;
					case "tbl_act_to_act":
						post['editstatus']='add';
						post['tbl_acm_child_id']=id_grid;
						post['tbl_acm_id']=id_act;
						post['bulan']=$('#bulan_main').val();
						post['tahun']=$('#tahun_main').val();
					break;
					case "tbl_act_to_act3":
						post['editstatus']='add';
						post['tbl_acm_child_id']=id_act;
						post['tbl_acm_id']=id_grid;
						post['bulan']=$('#bulan_main').val();
						post['tahun']=$('#tahun_main').val();
					break;
					case "tbl_act_to_act2":
						to="tbl_act_to_act";
						post['editstatus']='delete';
						post['id']=id_grid;
					break;
					
					case "tbl_emp":
						to="tbl_emp_act";
						post['editstatus']='delete';
						post['id']=id_grid;
					break;
					case "tbl_are_emp":
						to="tbl_are";
						post['editstatus']='delete';
						post['id']=id_grid;
					break;	
					case "tbl_efx":
						post['editstatus']='add';
						post['tbl_exp_id']=row.id;
					break;
					case "tbl_exp":
						to="tbl_efx";
						post['editstatus']='delete';
						post['id']=row.id;
					break;
					case "tbl_acm":
						to="tbl_bpd";
						post['editstatus']='delete';
						post['id']=row.id;
					break;
					case "tbl_bpd":
						post['editstatus']='add';
						post['tbl_acm_id']=row.id;
					break;
					
					//Modul Data Production
					case "tbl_prd":
						post['editstatus']='add';
						post['id']="";
						post['tbl_cdm_id']=row.id;
						post['tbl_prm_id']=tbl_prm_id;
						post['bulan']= bulan_pilihan;
						post['tahun']= tahun_pilihan;
					break;
					//End Modul
				}
			
			
			
			if(flag_oke == 'jenonk'){
				var urlnyacrot = host+'homex/simpansavedata/'+to;
			}else{
				var urlnyacrot = host+'home/simpansavedata/'+to;
			}	
			
			$.post(urlnyacrot, post, function(r){
				if(r==1){
					winLoadingClose();
					if(flag_oke == 'jenonk'){
						$('#master_cdm').datagrid('reload');
						$('#'+grid_id_destination).edatagrid('reload');
					}else{
						$('#'+grid_id_to).edatagrid('reload');
					}
				}
				else{
					winLoadingClose();
					$.messager.alert('ABC System',"Transfer Data Failed",'error');
					console.log(r);
				}
			});
		}
		else{
			$.messager.alert('ABC System',"Please Select List",'error');
		}
	
}
function aktif_non(id,sts){
	loadingna();
		$.post(host+'home/set_model',{id:id,status:sts},function(r){
			var resp=JSON.parse(r);
			//if(r==1){
					$("#grid_100").datagrid('reload');
					//console.log(resp.id);
					$('#model_na').html(resp.nama_model.toUpperCase());
					winLoadingClose();
			//}
			//else{
				//alert(r);winLoadingClose();	
			//}
		});
}

function NumberFormat(value) {
	
    var jml= new String(value);
    if(jml=="null" || jml=="NaN") jml ="0";
    jml1 = jml.split("."); 
    jml2 = jml1[0];
    amount = jml2.split("").reverse();

    var output = "";
    for ( var i = 0; i <= amount.length-1; i++ ){
        output = amount[i] + output;
        if ((i+1) % 3 == 0 && (amount.length-1) !== i)output = '.' + output;
    }
    //if(jml1[1]===undefined) jml1[1] ="00";
   // if(isNaN(output))  output = "0";
    return output; // + "." + jml1[1];
}

function pilih_tree(select_tor,id,mod,tahun){
	$('#'+select_tor).addClass('loading').html('');
	$.post(host+'konten',{ id:id,modul:mod,tahun:tahun },function(resp){
		$('#'+select_tor).removeClass('loading').html(resp);
	});
}


var seconds = 0, minutes = 0, hours = 0,t,waktu

function hitung_waktu(){
	t = setTimeout(add, 1000);
}

function hapus_waktu() {
    $('#waktu_na').html("00:00:00")
    seconds = 0; minutes = 0; hours = 0;
}

function add() {
	//var res;
    seconds++;
    if (seconds >= 60) {
        seconds = 0;
        minutes++;
        if (minutes >= 60) {
            minutes = 0;
            hours++;
        }
    }
    
    waktu = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);
	$('#waktu_na').html(waktu);
    hitung_waktu();
}
function refreshCaptcha(imgCapcha){
	capcha = $('#'+imgCapcha);
	capcha.css({"background-image":"url('"+host+"capcha/"+Math.random()+"')"});	
}
function hapus_file(mod,id){
	loadingna();
	$.post(host+'hapusFile',{mod:mod,id:id},function(r){
		if(r==1){
			winLoadingClose();
			$('#list_'+id).remove();
		}else{
			console.log(r);
			winLoadingClose();
			$.messager.alert('Bimbel',"Gagal Menghapus File",'error');
		}
	});
}
function myformatter(date){
	var y = date.getFullYear();
    var m = date.getMonth()+1;
    var d = date.getDate();
    return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
}
function myparser(s){
    if (!s) return new Date();
    var ss = (s.split('-'));
    var y = parseInt(ss[0],10);
    var m = parseInt(ss[1],10);
    var d = parseInt(ss[2],10);
    if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
        return new Date(y,m-1,d);
    } else {
        return new Date();
    }
}

function upload_video(obj,id_mat,flag_vid,id_judul){
	if(obj.getFileCount() > 0){
		jml_vid=jml_vid+1;
		console.log($('#'+id_judul).val());
		if($('#'+id_judul).val()!=""){
			obj.update({ dynamicFormData:function()
				{
					var data ={ id_materi:id_mat,flag:flag_vid,judul:$('#'+id_judul).val() }
					return data;
				} 
			});
			obj.startUpload();
			sts_vid=true;
		}else{
			sts_vid=false;
			winLoadingClose();
			//$.messager.alert('Bimbel',"Harap Berikan Judul Pada Video Yang akan DiUpload",'error');
		}
	}
}
var newWindow;
function openWindowWithPost(url,params)
{
    var x = Math.floor((Math.random() * 10) + 1);
	
	if (!newWindow || typeof(newWindow)=="undefined"){
		newWindow = window.open(url, 'winpost'); 
	}else{
		newWindow.close();
		newWindow = window.open(url, 'winpost'); 
		//return false;
	}
	
	var formid= "formid"+x;
    var html = "";
    html += "<html><head></head><body><form  id='"+formid+"' method='post' action='" + url + "'>";

    $.each(params, function(key, value) {
        if (value instanceof Array || value instanceof Object) {
            $.each(value, function(key1, value1) { 
                html += "<input type='hidden' name='" + key + "["+key1+"]' value='" + value1 + "'/>";
            });
        }else{
            html += "<input type='hidden' name='" + key + "' value='" + value + "'/>";
        }
    });
   
    html += "</form><script type='text/javascript'>document.getElementById(\""+formid+"\").submit()</script></body></html>";
    newWindow.document.write(html);
    return newWindow;
}
function gen_editor(id){
	tinymce.init({
		  selector: id,
		  height: 100,
		  plugins: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen tiny_mce_wiris",
				"insertdatetime media table contextmenu paste jbimages"
		    ],
			
		  // ===========================================
		  // PUT PLUGIN'S BUTTON on the toolbar
		  // ===========================================
		  menubar: false,
		  toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ",
			
		  // ===========================================
		  // SET RELATIVE_URLS to FALSE (This is required for images to display properly)
		  // ===========================================
			
		  relative_urls: false
		});
		
		tinyMCE.execCommand('mceRemoveControl', true, id);
		tinyMCE.execCommand('mceAddControl', true, id);
	
}