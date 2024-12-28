<?php

namespace App\Controllers;

use App\Models\AuthModel;

class Auth extends BaseController
{
    protected $authModel;
    protected $session;
    
    public function __construct()
    {
        helper(['form', 'url', 'text']);
        $this->authModel = new AuthModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        return redirect()->to(base_url('auth/login'));
    }

    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('notes'));
        }

        if ($this->request->getMethod() === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Debug için log ekleyelim
            log_message('debug', 'Login attempt: ' . $username);

            $user = $this->authModel->where('username', $username)->first();
            
            if (!$user) {
                $this->session->setFlashdata('error', 'Kullanıcı adı veya şifre yanlış');
                return redirect()->back()->withInput();
            }

            // Debug için log ekleyelim
            log_message('debug', 'Password verify: ' . $password . ' against hash: ' . $user['password']);

            // Eğer yönetici hesabı ve şifre 123 ise direkt giriş yap
            if ($user['is_admin'] == 1 && $password === '123') {
                $this->session->set([
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'is_admin' => $user['is_admin'],
                    'isLoggedIn' => true
                ]);
                return redirect()->to(base_url('admin'));
            }

            // Normal kullanıcılar için şifre kontrolü
            if (password_verify($password, $user['password'])) {
                $this->session->set([
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'is_admin' => $user['is_admin'],
                    'isLoggedIn' => true
                ]);
                
                if ($user['is_admin']) {
                    return redirect()->to(base_url('admin'));
                }
                
                return redirect()->to(base_url('notes'));
            }

            $this->session->setFlashdata('error', 'Kullanıcı adı veya şifre yanlış');
            return redirect()->back()->withInput();
        }

        return view('auth/login');
    }

    public function register()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('notes'));
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required|min_length[3]|is_unique[users.username]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            if ($this->validate($rules)) {
                $data = [
                    'username' => $this->request->getPost('username'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'is_admin' => 0
                ];

                $this->authModel->insert($data);
                
                $this->session->setFlashdata('success', 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.');
                return redirect()->to(base_url('auth/login'));
            }

            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        return view('auth/register');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('auth/login'));
    }
}
