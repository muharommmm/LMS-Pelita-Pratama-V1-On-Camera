<div class="container pt-4">
    <div class="info-box bg-transparent shadow-none">
        <?php
        $logo_app = $setting->logo_kanan == null ? base_url() . 'assets/img/favicon.png' : base_url() . $setting->logo_kanan;
        ?>
        <img src="<?= $logo_app ?>" width="60" height="60">
        <div class="info-box-content ml-2" style="text-shadow: 1px 1px 2px #050505ff">
            <h5 class="info-box-text text-wrap" style="color:#1A6B34;"> <b><?= $setting->nama_aplikasi ?></b> </h5>
            <span class="info-box-text" style="color:#1A6B34;"> <?= $setting->alamat ?>
</span>

        </div>
    </div>
    <div class="w-full max-w-sm mx-auto mt-20 p-8 rounded-[2rem] bg-white/10 backdrop-blur-sm border border-white/30 shadow-2xl relative z-10">
    <div class="text-center mb-6">
        <img src="<?php echo base_url('assets/img/login.png'); ?>" alt="Logo" class="max-w-[100px] mx-auto mb-3 drop-shadow-md">
        <p class="text-xs text-slate-700 tracking-widest font-bold drop-shadow-sm">L O G I N</p>
    </div>
    
    <div id="infoMessage" class="text-center mb-4 text-sm font-medium"><?php echo $message; ?></div>

    <?php 
    $identity['class'] = 'w-full pl-10 pr-4 py-3 rounded-full bg-white/20 border border-white/30 focus:outline-none focus:bg-white/90 focus:ring-2 focus:ring-blue-400 text-slate-800 shadow-inner transition-all placeholder-slate-500';
    $password['class'] = 'w-full pl-10 pr-10 py-3 rounded-full bg-white/20 border border-white/30 focus:outline-none focus:bg-white/90 focus:ring-2 focus:ring-blue-400 text-slate-800 shadow-inner transition-all placeholder-slate-500';
    ?>

    <?= form_open("auth/cek_login", array('id' => 'login', 'class' => 'space-y-4')); ?>
        
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="fas fa-user text-slate-500 group-focus-within:text-blue-500 transition-colors"></span>
            </div>
            <?= form_input($identity, '', 'required'); ?>
            <div class="help-block text-[11px] text-red-500 font-medium mt-1 ml-4 absolute -bottom-5"></div>
        </div>

        <div class="relative group mt-6">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="fas fa-lock text-slate-500 group-focus-within:text-blue-500 transition-colors"></span>
            </div>
            <?= form_input($password, '', 'required'); ?>
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
<script src="<?= base_url() ?>/assets/app/js/jquery.backstretch.js"></script>
<script type="text/javascript">
    let base_url = '<?=base_url();?>';
    var img = ["2.png", "3.png", "4.png"];

    $.backstretch([
        base_url + 'assets/img/' + img[0],
        base_url + 'assets/img/' + img[1],
        base_url + 'assets/img/' + img[2]
    ], {
        fade: 1000,
        duration: 10000
    });

    $(document).ready(function(){
        $('#myCarousel').carousel({
            interval: 1000 * 2,
            pause: 'none'
        });

        $('form#login input').on('change', function(){
            $(this).parent().removeClass('has-error');
            $(this).next().next().text('');
        });

        $('form#login').on('submit', function(e){
            e.preventDefault();
            e.stopImmediatePropagation();

            var infobox = $('#infoMessage');
            infobox.addClass('info-box align-items-center justify-content-center bg-gradient-info').text('Checking...');

            var btnsubmit = $('#submit');
            btnsubmit.attr('disabled', 'disabled').val('Wait...');

            const arrForm = $(this).serializeArray()
            const cbtOnly = arrForm.find(function (obj) {
                return obj.name === 'cbt-only'
            })
            localStorage.setItem('garudaCBT.login', cbtOnly !== undefined ? '1' : '0')

            $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(data){
                infobox.removeAttr('class').text('');
                btnsubmit.removeAttr('disabled').val('Login');
                console.log('login', data);
                if(data.status){
                    infobox.addClass('info-box align-items-center justify-content-center bg-gradient-success').text('Login Sukses');

                    const isLogin = localStorage.getItem('garudaCBT.login')
                    const isCbtMode = isLogin ? isLogin === '1' : false
                    let go = base_url + data.url;
                    if (isCbtMode && data.role === 'siswa') {
                        go = 'dashboard'
                    }
                    window.location.href = go;
                }else{
                    if(data.invalid){
                        $.each(data.invalid, function(key, val){
                        $('[name="'+key+'"').parent().addClass('has-error');
                        $('[name="'+key+'"').next().next().text(val);
                        if(val == ''){
                            $('[name="'+key+'"').parent().removeClass('has-error');
                            $('[name="'+key+'"').next().next().text('');
                        }
                        });
                    }
                        if(data.failed){
                            infobox.addClass('info-box align-items-center justify-content-center bg-gradient-danger').text(data.failed);
                        }
                    }
                }
            });
        });

        $('#toggle-password').on('click', function (e) {
            // toggle the type attribute
            const type = $('#password').attr('type') === 'password' ? 'text' : 'password';
            $('#password').attr('type', type);
            // toggle the eye / eye slash icon
            $(this).toggleClass('fa-eye-slash fa-eye');
        });
    });
</script>
