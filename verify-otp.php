<?php
$VERIFY_LOGIN = true;
require_once('includes/db.php');
$page_name = 'Login';

$CSS_FILES_ = [
    "login.css",
    _DIR_ . "/css/mdb.min.css"
];
$JS_FILES_ = [
    _DIR_ . "/js/mdb.min.js"
];

$session = isset($_SESSION["pending_2fa"])  ? $_SESSION["pending_2fa"] : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        .form-outline .form-control.active~.form-label,
        .form-outline .form-control:focus~.form-label {
            transform: translateY(-1.3rem) translateY(.1rem) scale(.8);
        }

        input {
            font-size: 18px !important;
        }

        label {
            font-size: 18px !important;
        }

        input {
            padding: 10px !important;
        }

        .container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .otp-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 30px 0;
        }

        .otp-input {
            width: 55px;
            height: 65px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            border: 2px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            transition: all 0.3s;
        }

        .otp-input:focus {
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            outline: none;
        }

        .otp-input.filled {
            border-color: #4CAF50;
            background-color: #f0fff0;
        }

        .info-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .timer {
            font-size: 16px;
            font-weight: 600;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        .btn {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            margin: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.6);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-resend {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
            box-shadow: none;
        }

        .btn-resend:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        .btn-resend:disabled {
            color: #aaa;
            border-color: #ddd;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-resend:disabled:hover {
            background: transparent;
        }

        .message {
            margin-top: 20px;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            .otp-input {
                width: 45px;
                height: 55px;
                font-size: 24px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="row mx-0 main-container">
            <div class="login-form content-center">
                <div class="login-form-container">
                    <div class="logo-img-head">
                        <img src="./images/autopro_logoo.png" class="logo-img" alt="Logo Img">
                    </div>
                    <div class="heading">
                        <a href="<?= isset($_GET['type']) ? "forgot" : "login" ?>">
                            <h3 style="font-size: 14px;"><i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp; Back</h3>
                        </a>
                        <h3 class="mt-4 text-center mb-2">Enter OTP <?= ENV === "local" ? arr_val($session, "otp", "") : "" ?></h3>
                        <p class="text-center">We shared a code to your registered email address <?= arr_val($session, "email", "") ?></p>
                    </div>
                    <form action="authorize" method="POST" class="mt-5 ajax_form">
                        <div class="otp-container">
                            <input type="text" class="otp-input" maxlength="1" data-index="0">
                            <input type="text" class="otp-input" maxlength="1" data-index="1">
                            <input type="text" class="otp-input" maxlength="1" data-index="2">
                            <input type="text" class="otp-input" maxlength="1" data-index="3">
                            <input type="text" class="otp-input" maxlength="1" data-index="4">
                            <input type="text" class="otp-input" maxlength="1" data-index="5">
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="otp" value="">
                            <input type="hidden" name="verify_otp" value="<?php echo md5(time()); ?>">
                            <?php if (isset($_GET['type'])) { ?>
                                <input type="hidden" name="forgot_password" value="<?php echo md5(time()); ?>">
                            <?php } ?>
                            <button type="submit" class="login-btn">Verify</button>
                        </div>
                    </form>
                    <p class="foot-param">© 2024 AutoPro Vehicle Management System.
                        All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>


    <?php require_once('./includes/js.php'); ?>
    <script>
        $(document).ready(function() {
            const otpInputs = $('.otp-input');
            const verifyBtn = $('#verifyBtn');
            const resendBtn = $('#resendBtn');
            const timerDisplay = $('#timer');
            const messageDiv = $('#message');

            let timer = 120; // 2 minutes
            let timerInterval;

            // Start the timer
            startTimer();

            // Focus first input
            otpInputs.eq(0).focus();
            let = numberOTP = "";

            // Handle OTP input
            otpInputs.on('input', function() {
                const value = $(this).val();
                const index = parseInt($(this).data('index'));

                // Only numbers
                if (!/^\d*$/.test(value)) {
                    $(this).val('');
                    return;
                }

                // Auto move next
                if (value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs.eq(index + 1).focus();
                }

                updateFilledClass();
                updateOTPValues();
            });

            function updateOTPValues() {
                numberOTP = ""; // reset

                otpInputs.each(function() {
                    numberOTP += $(this).val();
                });

                // Hidden input me OTP store
                $("input[name='otp']").val(numberOTP);

                // verify_otp ko bhi update karna
                const newVerifyOtp = md5(Date.now());
                $("input[name='verify_otp']").val(newVerifyOtp);
            }
            // Enable paste into first OTP box (auto fill all 6)
            otpInputs.on('paste', function(e) {
                e.preventDefault();

                let pasteData = (e.originalEvent || e).clipboardData.getData('text');

                // Only digits allowed
                pasteData = pasteData.replace(/\D/g, '');

                if (pasteData.length === 0) return;

                // Split digits into inputs
                otpInputs.each(function(index) {
                    $(this).val(pasteData[index] ?? "");
                });

                // Move focus to last filled box
                let filledCount = pasteData.length > 6 ? 6 : pasteData.length;
                otpInputs.eq(filledCount - 1).focus();

                updateFilledClass();
                updateOTPValues();
            });


            // Handle keydown (backspace, arrows)
            otpInputs.on('keydown', function(e) {
                const index = parseInt($(this).data('index'));

                if (e.key === "Backspace") {
                    if ($(this).val() === '' && index > 0) {
                        otpInputs.eq(index - 1).focus();
                    }
                    setTimeout(updateFilledClass, 10);
                }

                if (e.key === "ArrowLeft" && index > 0) {
                    otpInputs.eq(index - 1).focus();
                }

                if (e.key === "ArrowRight" && index < otpInputs.length - 1) {
                    otpInputs.eq(index + 1).focus();
                }
            });

            // Disable paste
            otpInputs.on('paste', function(e) {
                e.preventDefault();
            });

            // Get OTP
            function getOTP() {
                let otp = '';
                otpInputs.each(function() {
                    otp += $(this).val();
                });
                return otp;
            }

            // Update filled class
            function updateFilledClass() {
                otpInputs.each(function() {
                    if ($(this).val()) {
                        $(this).addClass('filled');
                    } else {
                        $(this).removeClass('filled');
                    }
                });
            }

            // Show message
            function showMessage(text, type) {
                messageDiv.text(text)
                    .removeClass()
                    .addClass('message ' + type)
                    .show();

                setTimeout(() => {
                    messageDiv.hide();
                }, 3000);
            }

            // Timer function
            function startTimer() {
                clearInterval(timerInterval);

                timerInterval = setInterval(() => {
                    timer--;

                    const minutes = Math.floor(timer / 60);
                    const seconds = timer % 60;

                    timerDisplay.text(
                        minutes.toString().padStart(2, '0') + ":" +
                        seconds.toString().padStart(2, '0')
                    );

                    if (timer <= 0) {
                        clearInterval(timerInterval);
                        resendBtn.prop('disabled', false);
                        timerDisplay.text("00:00");
                    }
                }, 1000);
            }
        });
    </script>

</body>

</html>