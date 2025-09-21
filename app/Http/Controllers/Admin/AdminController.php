<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
        // Fungsi untuk menampilkan dasbor admin
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Fungsi untuk menampilkan halaman Findings
    public function findings()
    {
        return view('admin.findings');
    }

    // Fungsi untuk menampilkan halaman Report
    public function report()
    {
        return view('admin.report');
    }

    // Fungsi untuk menampilkan halaman Manage Users
    public function manageUsers()
    {
        return view('admin.manage-users');
    }
}
