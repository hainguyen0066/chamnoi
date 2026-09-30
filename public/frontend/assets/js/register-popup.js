/**
 * Popup đăng ký/đăng nhập — AJAX flow:
 *  1. Gửi OTP: POST /otp/send {phone} -> hiện ô OTP + đếm ngược 60s gửi lại.
 *  2. Xác thực: POST /otp/verify {phone, otp} -> khoá phone/otp, hiện badge ✓.
 *  3. Đăng ký: POST /dang-ky-popup (chỉ khi đã verify OTP + đủ 18 tuổi).
 *  4. Đăng nhập: POST /dang-nhap-popup {username, password}.
 * Config nạp từ biến AUTH_POPUP_CONFIG (in trong _popups.blade.php).
 */
(function () {
    'use strict';

    if (typeof AUTH_POPUP_CONFIG === 'undefined') return;

    var cfg = AUTH_POPUP_CONFIG;
    var isPhoneVerified = false;
    var resendTimer = null;

    function $id(id) { return document.getElementById(id); }

    function postJson(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': cfg.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        }).then(function (res) { return res.json(); });
    }

    // Laravel validate lỗi 422 trả {message, errors:{field:[msg]}} — lấy msg đầu tiên.
    function extractMessage(json) {
        if (json.errors) {
            for (var key in json.errors) {
                if (json.errors[key] && json.errors[key].length) return json.errors[key][0];
            }
        }
        return json.message || 'Có lỗi xảy ra, vui lòng thử lại.';
    }

    function showMessage(elId, text, isSuccess) {
        var el = $id(elId);
        if (!el) return;
        el.textContent = text;
        el.style.display = 'block';
        el.style.color = isSuccess ? '#1a7a3a' : '#c0392b';
    }

    function clearMessage(elId) {
        var el = $id(elId);
        if (!el) return;
        el.textContent = '';
        el.style.display = 'none';
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function isValidPhone(phone) {
        return /^(09|03|05|07|08)[0-9]{8}$/.test(phone);
    }

    function calcAge(birthday) {
        if (!birthday) return null;
        var diff = Date.now() - new Date(birthday).getTime();
        return Math.floor(diff / 31557600000);
    }

    // Ghép 3 select ngày/tháng/năm thành YYYY-MM-DD, trả '' nếu chưa đủ.
    function getBirthday() {
        var d = $id('reg-birthday-day');
        var m = $id('reg-birthday-month');
        var y = $id('reg-birthday-year');
        if (!d || !m || !y || !d.value || !m.value || !y.value) return '';
        var dd = d.value.length < 2 ? '0' + d.value : d.value;
        var mm = m.value.length < 2 ? '0' + m.value : m.value;
        return y.value + '-' + mm + '-' + dd;
    }

    function startResendCountdown() {
        var btn = $id('btn-send-otp');
        var remaining = cfg.otpResendSeconds;
        btn.disabled = true;
        btn.style.opacity = '.6';
        btn.textContent = 'Gửi lại (' + remaining + 's)';

        resendTimer = setInterval(function () {
            remaining--;
            if (remaining <= 0) {
                clearInterval(resendTimer);
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.textContent = 'Gửi lại OTP';
                return;
            }
            btn.textContent = 'Gửi lại (' + remaining + 's)';
        }, 1000);
    }

    document.addEventListener('DOMContentLoaded', function () {

        // ---------- Validate birthday khi chọn bất kỳ select ngày/tháng/năm ----------
        function onBirthdayChange() {
            var bday = getBirthday();
            if (!bday) return; // chưa chọn đủ 3, chưa validate
            var age = calcAge(bday);
            if (age !== null && age < cfg.minAge) {
                showMessage('register-message', 'Người chơi đăng ký phải đủ 18 tuổi trở lên.', false);
            } else {
                clearMessage('register-message');
            }
        }
        ['reg-birthday-day', 'reg-birthday-month', 'reg-birthday-year'].forEach(function (id) {
            var el = $id(id);
            if (el) el.addEventListener('change', onBirthdayChange);
        });

        // ---------- 1. Gửi OTP ----------
        var btnSendOtp = $id('btn-send-otp');
        if (btnSendOtp) {
            btnSendOtp.addEventListener('click', function () {
                if (btnSendOtp.disabled) return;

                var phone = ($id('reg-phone').value || '').trim();
                if (!phone) {
                    showMessage('register-message', 'Vui lòng nhập số điện thoại.', false);
                    return;
                }
                if (!isValidPhone(phone)) {
                    showMessage('register-message', 'Số điện thoại không đúng định dạng (10 số, đầu số 03/05/07/08/09).', false);
                    return;
                }

                btnSendOtp.disabled = true;
                btnSendOtp.textContent = 'Đang gửi...';

                postJson(cfg.sendOtpUrl, { phone: phone }).then(function (json) {
                    if (json.success) {
                        showMessage('register-message', json.message, true);
                        $id('otp-group').style.display = 'block';
                        startResendCountdown();
                    } else {
                        showMessage('register-message', extractMessage(json), false);
                        btnSendOtp.disabled = false;
                        btnSendOtp.textContent = 'Gửi OTP';
                    }
                }).catch(function () {
                    showMessage('register-message', 'Không thể kết nối máy chủ, vui lòng thử lại.', false);
                    btnSendOtp.disabled = false;
                    btnSendOtp.textContent = 'Gửi OTP';
                });
            });
        }

        // ---------- 2. Xác thực OTP ----------
        var btnVerifyOtp = $id('btn-verify-otp');
        if (btnVerifyOtp) {
            btnVerifyOtp.addEventListener('click', function () {
                var phone = ($id('reg-phone').value || '').trim();
                var otp = ($id('reg-otp').value || '').trim();

                if (!/^[0-9]{6}$/.test(otp)) {
                    showMessage('register-message', 'Mã OTP gồm 6 chữ số.', false);
                    return;
                }

                btnVerifyOtp.disabled = true;

                postJson(cfg.verifyOtpUrl, { phone: phone, otp: otp }).then(function (json) {
                    if (json.success) {
                        isPhoneVerified = true;
                        showMessage('register-message', json.message, true);

                        $id('reg-phone').readOnly = true;
                        $id('reg-otp').readOnly = true;
                        btnVerifyOtp.style.display = 'none';
                        $id('btn-send-otp').style.display = 'none';
                        $id('otp-verified-badge').style.display = 'inline';
                        if (resendTimer) clearInterval(resendTimer);
                    } else {
                        showMessage('register-message', extractMessage(json), false);
                        btnVerifyOtp.disabled = false;
                    }
                }).catch(function () {
                    showMessage('register-message', 'Không thể kết nối máy chủ, vui lòng thử lại.', false);
                    btnVerifyOtp.disabled = false;
                });
            });
        }

        // ---------- 3. Đăng ký ----------
        var btnRegister = $id('btn-register-submit');
        if (btnRegister) {
            btnRegister.addEventListener('click', function () {
                var fullname = ($id('reg-fullname').value || '').trim();
                var birthday = getBirthday();
                var phone    = ($id('reg-phone').value || '').trim();
                var email    = ($id('reg-email').value || '').trim();
                var username = ($id('reg-username').value || '').trim();
                var password = $id('reg-password').value;
                var gender   = $id('reg-gender').value;

                // Kiểm tra lần lượt từng trường bắt buộc.
                if (!fullname) {
                    showMessage('register-message', 'Vui lòng nhập họ tên.', false); return;
                }
                if (!birthday) {
                    showMessage('register-message', 'Vui lòng nhập ngày sinh.', false); return;
                }
                var age = calcAge(birthday);
                if (age === null || age < cfg.minAge) {
                    showMessage('register-message', 'Người chơi đăng ký phải đủ 18 tuổi trở lên.', false); return;
                }
                if (!phone) {
                    showMessage('register-message', 'Vui lòng nhập số điện thoại.', false); return;
                }
                if (!isValidPhone(phone)) {
                    showMessage('register-message', 'Số điện thoại không đúng định dạng (10 số, đầu số 03/05/07/08/09).', false); return;
                }
                if (!isPhoneVerified) {
                    showMessage('register-message', 'Vui lòng xác thực số điện thoại qua OTP trước khi đăng ký.', false); return;
                }
                if (!email) {
                    showMessage('register-message', 'Vui lòng nhập email.', false); return;
                }
                if (!isValidEmail(email)) {
                    showMessage('register-message', 'Email không đúng định dạng.', false); return;
                }
                if (!username) {
                    showMessage('register-message', 'Vui lòng nhập tên đăng nhập.', false); return;
                }
                if (!password) {
                    showMessage('register-message', 'Vui lòng nhập mật khẩu.', false); return;
                }
                if (!gender) {
                    showMessage('register-message', 'Vui lòng chọn giới tính.', false); return;
                }

                btnRegister.style.pointerEvents = 'none';

                postJson(cfg.registerUrl, {
                    fullname: fullname,
                    birthday: birthday,
                    phone: phone,
                    email: email,
                    username: username,
                    password: password,
                    gender: gender,
                    address: ($id('reg-address').value || '').trim()
                }).then(function (json) {
                    if (json.success) {
                        showMessage('register-message', json.message + ' Đang chuyển hướng...', true);
                        window.location.href = json.redirect || '/';
                    } else {
                        showMessage('register-message', extractMessage(json), false);
                        btnRegister.style.pointerEvents = 'auto';
                    }
                }).catch(function () {
                    showMessage('register-message', 'Không thể kết nối máy chủ, vui lòng thử lại.', false);
                    btnRegister.style.pointerEvents = 'auto';
                });
            });
        }

        // ---------- 4. Đăng nhập ----------
        var btnLogin = $id('btn-login-submit');
        if (btnLogin) {
            btnLogin.addEventListener('click', function () {
                var username = ($id('login-username').value || '').trim();
                var password = $id('login-password').value;

                if (!username) {
                    showMessage('login-message', 'Vui lòng nhập tên đăng nhập hoặc email.', false); return;
                }
                if (!password) {
                    showMessage('login-message', 'Vui lòng nhập mật khẩu.', false); return;
                }

                btnLogin.style.pointerEvents = 'none';

                postJson(cfg.loginUrl, { username: username, password: password }).then(function (json) {
                    if (json.success) {
                        showMessage('login-message', json.message + ' Đang chuyển hướng...', true);
                        window.location.href = json.redirect || '/';
                    } else {
                        showMessage('login-message', extractMessage(json), false);
                        btnLogin.style.pointerEvents = 'auto';
                    }
                }).catch(function () {
                    showMessage('login-message', 'Không thể kết nối máy chủ, vui lòng thử lại.', false);
                    btnLogin.style.pointerEvents = 'auto';
                });
            });

            $id('login-password').addEventListener('keydown', function (e) {
                if (e.key === 'Enter') btnLogin.click();
            });
        }
    });
})();
