<?php

namespace App\Controllers;

class PageController
{
    /**
     * Shows a generic "Coming Soon" page.
     */
    private function comingSoon(string $title)
    {
        $data = ['title' => $title . ' - Coming Soon'];
        return view('pages.coming_soon', $data);
    }
    
    public function series() { return $this->comingSoon('Series'); }
    public function community() { return $this->comingSoon('Community'); }
    public function about() { return view('pages.about', ['title' => 'About Us - VillaStudio']); }
    public function contact() { return view('pages.contact', ['title' => 'Contact Us - VillaStudio']); }
    public function faq() { return view('pages.faq', ['title' => 'FAQ - VillaStudio']); }
    public function terms() { return view('pages.terms', ['title' => 'Terms of Service - VillaStudio']); }
    public function privacy() { return view('pages.privacy', ['title' => 'Privacy Policy - VillaStudio']); }
    public function copyright() { return view('pages.copyright', ['title' => 'Copyright - VillaStudio']); }
}