<?php
/*   ________________________________________
    |                 GarudaCBT              |
    |    https://github.com/garudacbt/cbt    |
    |________________________________________|
*/
 defined("\x42\x41\123\105\120\101\124\x48") or exit("\116\x6f\40\144\x69\x72\145\143\x74\x20\163\x63\162\x69\x70\164\x20\x61\x63\x63\145\x73\163\40\x61\x6c\x6c\157\167\x65\x64"); class Welcome extends CI_Controller {
    public function index() {
        redirect('auth');
    }

    public function manifest() {
        $this->load->model('Settings_model', 'settings');
        $setting = $this->settings->getSetting();
        
        $logo = ($setting && !empty($setting->logo_kiri)) ? $setting->logo_kiri : 'assets/img/login.png';
        $logo_url = base_url($logo);
        
        $manifest = [
            "name" => "LMS Pelita Pratama",
            "short_name" => "Pelita LMS",
            "start_url" => base_url(),
            "display" => "standalone",
            "background_color" => "#334779",
            "theme_color" => "#334779",
            "icons" => [
                [
                    "src" => $logo_url,
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => $logo_url,
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ]
        ];
        
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}
