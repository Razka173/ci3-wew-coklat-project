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
		$this->load->model('alamat_pelanggan_model');
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

		$valid->set_rules('telepon','Nomor Telepon','required',
			array(	'required'		=> '%s harus diisi'));

		if($valid->run()===FALSE) {
		// End validasi

			$data = array(	'title'				=> 'Profil Saya',
							'pelanggan'			=> $pelanggan,
							'isi'				=> 'dasbor/profil'
						);
			$this->load->view('layout/wrapper', $data, FALSE);

		// Masuk database
		} else {
			$i = $this->input;
			$data = array(	'id_pelanggan'		=> $id_pelanggan,
							'nama_pelanggan'	=> $i->post('nama_pelanggan'),
							'telepon'			=> $i->post('telepon'),
						);
			// End data update
			$this->pelanggan_model->edit($data);
			$this->session->set_flashdata('sukses', 'Update Profil berhasil');
			redirect(base_url('dasbor/profil'),'refresh');
		}
		// End masuk database
	}

	// Ganti password
	public function password(){
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan 		= $this->session->userdata('id_pelanggan');
		$email 				= $this->session->userdata('email');
		$pelanggan 			= $this->pelanggan_model->detail($id_pelanggan);

		// Validasi input
		$valid = $this->form_validation;

		// $valid->set_rules('old_password','Password Lama','required',
		// 	array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('new_password','Password Baru','required|min_length[6]',
			array(	'required'		=> '%s harus diisi',
					'min_length'	=> '%s minimal 6 karakter'));

		$valid->set_rules('cfnew_password','Konfirmasi Password','required|matches[new_password]',
			array(	'required'		=> '%s harus diisi',
					'matches'		=> '%s tidak sama dengan password'));

		if($valid->run()===FALSE) {
		// End validasi

			$data = array(	'title'				=> 'Ganti Password',
							'pelanggan'			=> $pelanggan,
							'isi'				=> 'dasbor/password'
						);
			$this->load->view('layout/wrapper', $data, FALSE);

		// Masuk database
		} else {
			$i = $this->input;
			$old_password = $i->post('old_password');
			$check = $this->pelanggan_model->login($email, $old_password);

			if($check){
				$data = array(	'id_pelanggan'		=> $id_pelanggan,
								'password'			=> SHA1($i->post('new_password')),
							);
				// End data update
				$this->pelanggan_model->edit($data);
				$this->session->set_flashdata('sukses', 'Password berhasil diganti');
				redirect(base_url('dasbor/profil'),'refresh');
			} elseif( $pelanggan->oauth_provider=='google' and empty($pelanggan->password) and !empty($old_password) ) {
				$this->session->set_flashdata('warning', 'Anda pengguna google dan belum memiliki password. Kosongkan Kolom Password Lama untuk membuat password.');
				redirect(base_url('dasbor/password'),'refresh');
			} elseif( $pelanggan->oauth_provider=='google' ) {
				$data = array(	'id_pelanggan'		=> $id_pelanggan,
								'password'			=> SHA1($i->post('new_password')),
							);
				// End data update
				$this->pelanggan_model->edit($data);
				$this->session->set_flashdata('sukses', 'Password berhasil diganti');
				redirect(base_url('dasbor/profil'),'refresh');
			} else {
				$this->session->set_flashdata('warning', 'Password lama anda salah.');
				redirect(base_url('dasbor/password'),'refresh');
			}
		}
	}

	// HALAMAN BAYAR
	public function bayar($kode_transaksi)
	{
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan		= $this->session->userdata('id_pelanggan');
		$pelanggan 			= $this->pelanggan_model->detail($id_pelanggan);
		$rekening 			= $this->rekening_model->listing();
		$header_transaksi 	= $this->header_transaksi_model->kode_transaksi($kode_transaksi);

		$data = array(	'title'				=> 'Pembayaran Pesanan',
						'rekening'			=> $rekening,
						'pelanggan'			=> $pelanggan,
						'header_transaksi'	=> $header_transaksi,
						'isi'				=> 'dasbor/bayar'
					);
		$this->load->view('layout/wrapper', $data, FALSE);
	}

	// Konfirmasi pembayaran
	public function konfirmasi($kode_transaksi)
	{
		$id_pelanggan		= $this->session->userdata('id_pelanggan');
		$header_transaksi 	= $this->header_transaksi_model->kode_transaksi($kode_transaksi);
		$rekening 			= $this->rekening_model->listing();
		$rekening_pelanggan = $this->rekening_pelanggan_model->pelanggan($id_pelanggan);

		// Validasi input
		$valid 		= $this->form_validation;

		$valid->set_rules('tanggal_bayar','Tanggal Pembayaran','required',
			array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('jumlah_bayar','Jumlah Pembayaran','required',
			array(	'required'		=> '%s harus diisi'));

		$valid->set_rules('id_rekening_pelanggan','Metode Pembayaran','required',
			array(	'required'		=> '%s harus diisi'));
		
		if( $valid->run() ) {
			
			// Check jika gambar diganti
			if( !empty($_FILES['bukti_bayar']['name']) ) {
				$config['upload_path'] 		= './assets/upload/image/';
				$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
				$config['max_size']  		= '5400';//Dalam KB
				$config['max_width']  		= '3048';
				$config['max_height']  		= '3048';
			
				$this->load->library('upload', $config);
				
				// End validasi
				if ( !$this->upload->do_upload('bukti_bayar')) {

					$data = array(	'title'					=> 'Konfirmasi Pembayaran',
									'header_transaksi'		=> $header_transaksi,
									'rekening'				=> $rekening,
									'rekening_pelanggan'	=> $rekening_pelanggan,
									'error'					=> $this->upload->display_errors(),
									'isi'					=> 'dasbor/konfirmasi'
									);
					$this->load->view('layout/wrapper', $data, FALSE);

				// Masuk database
				}else {
					$upload_gambar = array( 'upload_data' => $this->upload->data() );

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
					if( $i->post('id_rekening_pelanggan')=='dana'){
						$nama_bank = 'via DANA';
						$nomor_rekening = '-';
						$nama_pemilik = '-';
					} else {
						$rekening_pelanggan_detail = $this->rekening_pelanggan_model->detail($i->post('id_rekening_pelanggan'));
						$nama_bank = $rekening_pelanggan_detail->nama_bank;
						$nomor_rekening = $rekening_pelanggan_detail->nomor_rekening;
						$nama_pemilik = $rekening_pelanggan_detail->nama_pemilik;
					}
					
					$data = array(	'id_header_transaksi'	=> $header_transaksi->id_header_transaksi,
									'status_bayar'			=> 'Konfirmasi',
									'jumlah_bayar'			=> $i->post('jumlah_bayar'),
									'nama_bank'				=> $nama_bank,
									'rekening_pembayaran'	=> $nomor_rekening,
									'rekening_pelanggan'	=> $nama_pemilik,
									'bukti_bayar'			=> $upload_gambar['upload_data']['file_name'],
									'id_rekening'			=> $i->post('id_rekening'),
									'tanggal_bayar'			=> $i->post('tanggal_bayar'),	
								);
					$this->header_transaksi_model->edit($data);
					$this->session->set_flashdata('sukses', 'Unggah Bukti Pembayaran Berhasil, Silahkan Menunggu Untuk Konfirmasi Bukti Bayar');
					redirect(base_url('dasbor'),'refresh');
				}
			}else {
				// Edit tanpa ganti gambar
				$i = $this->input;
				if( $i->post('id_rekening_pelanggan')=='dana'){
					$nama_bank = 'via DANA';
					$nomor_rekening = '-';
					$nama_pemilik = '-';
				} else {
					$rekening_pelanggan_detail = $this->rekening_pelanggan_model->detail($i->post('id_rekening_pelanggan'));
					$nama_bank = $rekening_pelanggan_detail->nama_bank;
					$nomor_rekening = $rekening_pelanggan_detail->nomor_rekening;
					$nama_pemilik = $rekening_pelanggan_detail->nama_pemilik;
				}
				$data = array(	'id_header_transaksi'	=> $header_transaksi->id_header_transaksi,
								'status_bayar'			=> 'Konfirmasi',
								'jumlah_bayar'			=> $i->post('jumlah_bayar'),
								'nama_bank'				=> $nama_bank,
								'rekening_pembayaran'	=> $nomor_rekening,
								'rekening_pelanggan'	=> $nama_pemilik,
								// 'bukti_bayar'		=> $upload_gambar['upload_data']['file_name'],
								'id_rekening'			=> $i->post('id_rekening'),
								'tanggal_bayar'			=> $i->post('tanggal_bayar'),

							);
				$this->header_transaksi_model->edit($data);
				$this->session->set_flashdata('sukses', 'Konfirmasi Pembayaran Berhasil');
				redirect(base_url('dasbor'),'refresh');
			}
		}
		// End masuk database
		$data = array(	'title'					=> 'Konfirmasi Pembayaran',
						'header_transaksi'		=> $header_transaksi,
						'rekening'				=> $rekening,
						'rekening_pelanggan'	=> $rekening_pelanggan,
						'isi'					=> 'dasbor/konfirmasi'
						);
		$this->load->view('layout/wrapper', $data, FALSE);
	}

	// Halaman Rekening Pelanggan
	public function rekening() 
	{
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

	// Halaman Alamat Pelanggan
	public function alamat() 
	{
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan		= $this->session->userdata('id_pelanggan');
		$pelanggan 			= $this->pelanggan_model->detail($id_pelanggan);
		$alamat_pelanggan 	= $this->alamat_pelanggan_model->pelanggan($id_pelanggan);

		$data = array(	'title'					=> 'Halaman Daftar Alamat Pelanggan',
						'alamat_pelanggan'		=> $alamat_pelanggan,
						'pelanggan'				=> $pelanggan,
						'isi'					=> 'dasbor/alamat'
					);
		$this->load->view('layout/wrapper', $data, FALSE);
	}

	// Halaman Informasi Rekening Pembayaran Website
	public function info() 
	{
		// Ambil data login id_pelanggan dari SESSION
		$id_pelanggan		= $this->session->userdata('id_pelanggan');
		$pelanggan 			= $this->pelanggan_model->detail($id_pelanggan);
		$rekening 			= $this->rekening_model->listing();

		$data = array(	'title'				=> 'Informasi Pembayaran',
						'rekening'			=> $rekening,
						'pelanggan'			=> $pelanggan,
						'isi'				=> 'dasbor/info'
					);
		$this->load->view('layout/wrapper', $data, FALSE);
	}
}

/* End of file Dasbor.php */
/* Location: ./application/controllers/Dasbor.php */