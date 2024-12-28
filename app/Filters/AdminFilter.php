<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn') || $session->get('username') !== 'admin') {
            $session->setFlashdata('message', 'Bu sayfaya erişim yetkiniz yok.');
            $session->setFlashdata('type', 'danger');
            return redirect()->to(base_url('notes'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
