<!DOCTYPE html><html lang="id" style=""><head><meta charset="utf-8"><meta content="width=device-width, initial-scale=1.0" name="viewport"><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script></head><body class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 relative bg-black">
<!-- Full Screen Background (handled by backstretch in JS) -->
<div class="fixed inset-0 z-[-1]">
<div class="absolute inset-0 bg-black/20"></div>
</div>
<!-- BEGIN: Main Login Container -->
<main class="w-full max-w-sm sm:max-w-md bg-white/10 backdrop-blur-xl border border-white/40 rounded-[24px] shadow-2xl p-8 sm:p-10" data-purpose="login-container">
<!-- Brand Header -->
<div class="mb-10 text-left" data-purpose="brand-header">
<h1 class="text-4xl font-bold text-white mb-2 tracking-tight">Login</h1>
<p class="text-base text-white/90">Welcome back please login to your account</p>
</div>
<!-- Info Message -->
<div class="mb-4 text-center text-sm font-medium text-white bg-red-500/50 rounded-lg p-2" id="infoMessage" style="display:none;"></div>
<!-- Login Form -->
<form id="login" class="space-y-6" action="auth/cek_login" method="POST">
            
            <!-- Username Field -->
<div>
<div class="relative">
<input name="identity" id="username" class="block w-full pl-5 pr-12 py-3.5 bg-transparent border border-white/60 rounded-2xl text-white placeholder-white/80 focus:ring-white/50 focus:border-white/80 sm:text-sm transition-colors" placeholder="User Name" required="" type="text" autofocus="">
<div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
<svg class="h-6 w-6 text-white/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
<path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
</div>
</div>
</div>
<!-- Password Field -->
<div>
<div class="relative">
<input name="password" id="password" class="block w-full pl-5 pr-12 py-3.5 bg-transparent border border-white/60 rounded-2xl text-white placeholder-white/80 focus:ring-white/50 focus:border-white/80 sm:text-sm transition-colors" placeholder="Password" required="" type="password">
<div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-white/80 hover:text-white transition-colors" id="toggle-password">
<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
<path d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
</div>
</div>
</div>
<!-- Options Row -->
<div class="flex items-center justify-between">
<div class="flex items-center">
<input class="h-5 w-5 bg-white/20 border-white/50 text-[#6a8738] focus:ring-[#6a8738] rounded checked:bg-white checked:border-white transition-colors" id="login-cbt" name="login-cbt" type="checkbox" value="">
<label class="ml-3 block text-sm font-medium text-white" for="login-cbt">Login CBT</label>
</div>
</div>
<!-- Submit Button -->
<div class="pt-2">
<input type="submit" name="submit" value="Login" id="submit" class="w-full flex justify-center py-3.5 px-4 border border-white/40 rounded-2xl shadow-sm text-lg font-bold text-white bg-gradient-to-r from-[#98ac63] to-[#6a8738] hover:from-[#8ba154] hover:to-[#5a7428] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6a8738] focus:ring-offset-transparent transition-all cursor-pointer" style="background: rgb(34, 55, 104);">
</div>
</form>
</main>
<script src="&lt;?= base_url() ?&gt;assets/plugins/jquery/jquery-3.7.1.min.js"></script>
<script src="&lt;?= base_url() ?&gt;assets/plugins/jquery.backstretch/jquery.backstretch.min.js"></script>
<script type="text/javascript">
        $(document).ready(function() {
            $.backstretch([
                "<?= base_url() ?>assets/img/login/login_1.jpg",
                "<?= base_url() ?>assets/img/login/login_2.jpg",
                "<?= base_url() ?>assets/img/login/login_3.jpg"
            ], { duration: 3000, fade: 750 });

            $('#toggle-password').click(function() {
                var input = $('#password');
                if (input.attr('type') == 'password') {
                    input.attr('type', 'text');
                    $(this).html('<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path></svg>');
                } else {
                    input.attr('type', 'password');
                    $(this).html('<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"></path></svg>');
                }
            });

            $('form#login').on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var infobox = $('#infoMessage');
                var btnsubmit = $('#submit');
                var loginCBT = $('#login-cbt').is(':checked') ? 1 : 0;
                infobox.closest('div').css('display', 'none');
                btnsubmit.attr('disabled', 'disabled').val('Wait...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize() + '&login-cbt=' + loginCBT,
                    success: function(data) {
                        if (data.status) {
                            infobox.closest('div').css('display', 'block');
                            infobox.html('<div class="alert alert-success">Login berhasil</div>');
                            window.location.href = data.url;
                        } else {
                            infobox.closest('div').css('display', 'block');
                            infobox.html(data.failed);
                        }
                        btnsubmit.removeAttr('disabled</script></body></html>