<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\views\auth\login.php';
$content = file_get_contents($file);

// Find the start of container-fluid h-100
$start_pos = strpos($content, '<div class="container-fluid h-100">');
if ($start_pos === false) {
    die("Start pattern not found.");
}

// Find the start of the script tag (which we must preserve)
$end_pos = strpos($content, '<script src="<?= base_url() ?>/assets/app/js/jquery.backstretch.js"></script>');
if ($end_pos === false) {
    die("End pattern not found.");
}

$new_html = <<<HTML
<div class="w-full max-w-sm mx-auto mt-20 p-8 rounded-[2rem] bg-white/30 backdrop-blur-md border border-white/40 shadow-2xl relative z-10">
    <div class="text-center mb-6">
        <img src="<?php echo base_url('assets/img/login.png'); ?>" alt="Logo" class="max-w-[100px] mx-auto mb-3 drop-shadow-md">
        <p class="font-bold text-xl text-slate-800 drop-shadow-sm mb-1">Pelita Pratama</p>
        <p class="text-xs text-slate-700 tracking-widest font-bold drop-shadow-sm">L O G I N</p>
    </div>
    
    <div id="infoMessage" class="text-center mb-4 text-sm font-medium"><?php echo \$message; ?></div>

    <?php 
    \$identity['class'] = 'w-full pl-10 pr-4 py-3 rounded-full bg-white/60 backdrop-blur-sm border border-white/50 focus:outline-none focus:bg-white/90 focus:ring-2 focus:ring-blue-400 text-slate-800 shadow-inner transition-all placeholder-slate-500';
    \$password['class'] = 'w-full pl-10 pr-10 py-3 rounded-full bg-white/60 backdrop-blur-sm border border-white/50 focus:outline-none focus:bg-white/90 focus:ring-2 focus:ring-blue-400 text-slate-800 shadow-inner transition-all placeholder-slate-500';
    ?>

    <?= form_open("auth/cek_login", array('id' => 'login', 'class' => 'space-y-4')); ?>
        
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="fas fa-user text-slate-500 group-focus-within:text-blue-500 transition-colors"></span>
            </div>
            <?= form_input(\$identity, '', 'required'); ?>
            <div class="help-block text-[11px] text-red-500 font-medium mt-1 ml-4 absolute -bottom-5"></div>
        </div>

        <div class="relative group mt-6">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="fas fa-lock text-slate-500 group-focus-within:text-blue-500 transition-colors"></span>
            </div>
            <?= form_input(\$password, '', 'required'); ?>
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                <span id="toggle-password" class="fas fa-eye-slash text-slate-500 cursor-pointer hover:text-slate-800 transition-colors"></span>
            </div>
            <div class="help-block text-[11px] text-red-500 font-medium mt-1 ml-4 absolute -bottom-5"></div>
        </div>

        <div class="flex items-center justify-between mt-8 pt-4">
            <div class="flex items-center">
                <input type="checkbox" id="cbt-only" name="cbt-only" value="1" checked="checked" class="w-4 h-4 text-blue-600 bg-white/50 border-white rounded focus:ring-blue-500 cursor-pointer">
                <label for="cbt-only" class="ml-2 text-sm font-bold text-slate-800 drop-shadow-sm cursor-pointer select-none">Login CBT</label>
            </div>
            
            <button type="submit" id="submit" class="px-6 py-2.5 bg-[#1c3664] text-white font-semibold rounded-full hover:bg-[#14284d] transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 duration-200 flex items-center gap-2">
                <?= lang('login_submit_btn') ?> <i class="fas fa-sign-in-alt"></i>
            </button>
        </div>
        
    <?= form_close(); ?>
</div>

HTML;

$new_content = substr($content, 0, $start_pos) . $new_html . substr($content, $end_pos);
file_put_contents($file, $new_content);

echo "Login card replaced with Glassmorphism Tailwind design.\n";
