<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dasbor extends CI_Controller {

	// Load model
	public function __construct()
	{
		parent::__construct();
		$this->load->model('pelanggan_model');
		$this->load->model('header_transaksi_model');
		$this->load->model('transaksi_model');
		$this->load->model('rekening_model');
		$this->load->model('rekening_pelanggan_model');
		// Halaman ini diproteksi dengan Simple_pelanggan => Check login
		$this->simple_pelanggan->cek_login();
	}

	public function index()
	{
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan		= $this->session->userdata('id_pelanggan');
		$pelanggan 			= $this->pelanggan_model->detail($id_pelanggan);
		$header_transaksi 	= $this->header_transaksi_model->pelanggan($id_pelanggan);

		$data = array(	'title'				=> 'Halaman Dasbor Pelanggan',
						'header_transaksi'	=> $header_transaksi,
						'pelanggan'			=> $pelanggan,
						'isi'				=> 'dasbor/list'
					);
		$this->load->view('layout/wrapper', $data, FALSE);
	}

	// Belanja
	public function belanja()
	{
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan = $this->session->userdata('id_pelanggan');
		$header_transaksi = $this->header_transaksi_model->pelanggan($id_pelanggan);

		$data = array(	'title'				=> 'Riwayat Belanja',
						'header_transaksi'	=> $header_transaksi,
						'isi'				=> 'dasbor/belanja'
					);
		$this->load->view('layout/wrapper', $data, FALSE);
	}

	// Detail
	public function detail($kode_transaksi)
	{
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan 		= $this->session->userdata('id_pelanggan');
		$header_transaksi 	= $this->header_transaksi_model->kode_transaksi($kode_transaksi);
		$transaksi 			= $this->transaksi_model->kode_transaksi($kode_transaksi);

		// Pastikan bahwa pelanggan hanya mengakses data transaksinya
		if($header_transaksi->id_pelanggan != $id_pelanggan) {
			$this->session->set_flashdata('warning', 'Anda mencoba mengakses data transaksi orang lain');
			redirect(base_url('masuk'));
		}
		

		$data = array(	'title'				=> 'Riwayat Belanja',
						'header_transaksi'	=> $header_transaksi,
						'transaksi'			=> $transaksi,
						'isi'				=> 'dasbor/detail'
					);
		$this->load->view('layout/wrapper', $data, FALSE);	
	}

	// Profil
	public function profil()
	{
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan 		= $this->session->userdata('id_pelanggan');
		$pelanggan 			= $this->pelanggan_model->detail($id_pelanggan);

		// Validasi input
		$valid = $this->form_validation;

		$valid->set_rules('nama_pelanggan','Nama lengkap','required',
			array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('alamat','Alamat lengkap','required',
			array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('telepon','Nomor Telepon','required',
			array(	'required'		=> '%s harus diisi'));

		// $valid->set_rules('password','Password','required|min_length[6]',
		// 	array(	'required'		=> '%s harus diisi',
		// 			'min_length'	=> '%s minimal 6 karakter'));

		if($valid->run()===FALSE) {
		// End validasi

		$data = array(	'title'				=> 'Profil Saya',
						'pelanggan'			=> $pelanggan,
						'isi'				=> 'dasbor/profil'
					);
		$this->load->view('layout/wrapper', $data, FALSE);	
		// Masuk database
		}else{
			$i = $this->input;
			// Kalau password lebih dari 6 karakter, maka password diganti
			if( strlen($i->post('password')) >= 6 ) {
				$data = array(	'id_pelanggan'		=> $id_pelanggan,
								'nama_pelanggan'	=> $i->post('nama_pelanggan'),
								'password'			=> SHA1($i->post('password')),
								'telepon'			=> $i->post('telepon'),
								'alamat'			=> $i->post('alamat'),
							);
			}else{
				// Kalau password kurang dari 6 maka password ga diganti
				$data = array(	'id_pelanggan'		=> $id_pelanggan,
								'nama_pelanggan'	=> $i->post('nama_pelanggan'),
								'telepon'			=> $i->post('telepon'),
								'alamat'			=> $i->post('alamat'),
							);
			}
			// End data update
			$this->pelanggan_model->edit($data);
			$this->session->set_flashdata('sukses', 'Update Profil berhasil');
			redirect(base_url('dasbor/profil'),'refresh');
		}
		// End masuk database
	}

	// Konfirmasi pembayaran
	public function konfirmasi($kode_transaksi)
	{
		$header_transaksi 	= $this->header_transaksi_model->kode_transaksi($kode_transaksi);
		$rekening 			= $this->rekening_model->listing();

		// Validasi input
		$valid 		= $this->form_validation;

		$valid->set_rules('nama_bank','Nama Bank','required',
			array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('rekening_pembayaran','Nomor Rekening','required',
			array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('rekening_pelanggan','Nama Pemilik Rekening','required',
			array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('tanggal_bayar','Tanggal Pembayaran','required',
			array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('jumlah_bayar','Jumlah Pembayaran','required',
			array(	'required'		=> '%s harus diisi'));
		
		if($valid->run()) {
			// Check jika gambar diganti
			if( !empty($_FILES['bukti_bayar']['name']) ) {
			$config['upload_path'] 		= './assets/upload/image/';
			$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
			$config['max_size']  		= '5400';//Dalam KB
			$config['max_width']  		= '3048';
			$config['max_height']  		= '3048';
			
			$this->load->library('upload', $config);
			
			if ( ! $this->upload->do_upload('bukti_bayar')){
				
		// End validasi

		$data = array(	'title'				=> 'Konfirmasi Pembayaran',
						'header_transaksi'	=> $header_transaksi,
						'rekening'			=> $rekening,
						'error'				=> $this->upload->display_errors(),
						'isi'				=> 'dasbor/konfirmasi'
						);
		$this->load->view('layout/wrapper', $data, FALSE);
		// Masuk database
		}else{
			$upload_gambar = array('upload_data' => $this->upload->data());

			// Create thumbnail gambar
			$config['image_library'] 	= 'gd2';
			$config['source_image'] 	= './assets/upload/image/'.$upload_gambar['upload_data']['file_name'];
			// lokasi folder thumbnail
			$config['new_image']		= './assets/upload/image/thumbs/';
			$config['create_thumb'] 	= TRUE;
			$config['maintain_ratio'] 	= TRUE;
			$config['width']         	= 250;//Pixel
			$config['height']       	= 250;//Pixel
			$config['thumb_marker']		= '';

			$this->load->library('image_lib', $config);

			$this->image_lib->resize();
			// End create thumbnail

			$i = $this->input;

			$data = array(	'id_header_transaksi'	=> $header_transaksi->id_header_transaksi,
							'status_bayar'			=> 'Konfirmasi',
							'jumlah_bayar'			=> $i->post('jumlah_bayar'),
							'rekening_pembayaran'	=> $i->post('rekening_pembayaran'),
							'rekening_pelanggan'	=> $i->post('rekening_pelanggan'),
							'bukti_bayar'			=> $upload_gambar['upload_data']['file_name'],
							'id_rekening'			=> $i->post('id_rekening'),
							'tanggal_bayar'			=> $i->post('tanggal_bayar'),
							'nama_bank'				=> $i->post('nama_bank'),
						);
			$this->header_transaksi_model->edit($data);
			$this->session->set_flashdata('sukses', 'Unggah Bukti Pembayaran Berhasil, Silahkan Menunggu Untuk Konfirmasi Bukti Bayar');
			redirect(base_url('dasbor'),'refresh');
		}}else{
			// Edit produk tanpa ganti gambar
			$i = $this->input;
			// SLUG PRODUK
			
			$data = array(	'id_header_transaksi'	=> $header_transaksi->id_header_transaksi,
							'status_bayar'			=> 'Konfirmasi',
							'jumlah_bayar'			=> $i->post('jumlah_bayar'),
							'rekening_pembayaran'	=> $i->post('rekening_pembayaran'),
							'rekening_pelanggan'	=> $i->post('rekening_pelanggan'),
							// 'bukti_bayar'		=> $upload_gambar['upload_data']['file_name'],
							'id_rekening'			=> $i->post('id_rekening'),
							'tanggal_bayar'			=> $i->post('tanggal_bayar'),
							'nama_bank'				=> $i->post('nama_bank'),
						);
			$this->header_transaksi_model->edit($data);
			$this->session->set_flashdata('sukses', 'Konfirmasi Pembayaran Berhasil');
			redirect(base_url('dasbor'),'refresh');
		}}
		// End masuk database
		$data = array(	'title'				=> 'Konfirmasi Pembayaran',
						'header_transaksi'	=> $header_transaksi,
						'rekening'			=> $rekening,
						'isi'				=> 'dasbor/konfirmasi'
						);
		$this->load->view('layout/wrapper', $data, FALSE);
	}

	// Halaman Rekening Pelanggan
	public function rekening(){
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan		= $this->session->userdata('id_pelanggan');
		$pelanggan 			= $this->pelanggan_model->detail($id_pelanggan);
		$rekening_pelanggan = $this->rekening_pelanggan_model->pelanggan($id_pelanggan);

		$data = array(	'title'					=> 'Halaman Rekening Bank Pelanggan',
						'rekening_pelanggan'	=> $rekening_pelanggan,
						'pelanggan'				=> $pelanggan,
						'isi'					=> 'dasbor/rekening'
					);
		$this->load->view('layout/wrapper', $data, FALSE);
	}
}

/* End of file Dasbor.php */
/* Location: ./application/controllers/Dasbor.php */