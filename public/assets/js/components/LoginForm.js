(function() {
    const h = React.createElement;

    function LoginForm({
        email, setEmail, password, setPassword, showPassword, setShowPassword,
        handleFlipToRegister, handleSubmit, error, shake
    }) {
        return h("div", { className: `new-form-container ${shake ? "shake" : ""}` },
            h("form", { onSubmit: handleSubmit, className: "fade-in" }, [
                // Header
                h("div", { key: "header", className: "mb-6" }, [
                    h("h2", { key: "title", className: "text-xl font-bold text-gray-900 mb-1" }, "Welcome back!"),
                    h("p", { key: "sub", className: "text-gray-500 text-xs" }, "Enter your credentials to access your account")
                ]),

                // Error
                error && h("div", { key: "err", className: "auth-error" }, 
                    h("p", { className: "auth-error-text" }, error)
                ),

                // Email
                h("div", { key: "email", className: "mb-3" }, [
                    h("label", { key: "l", className: "form-label" }, "Email Address"),
                    h("input", { 
                        key: "i", type: "text", 
                        className: `form-input ${error && !email ? "border-red-500" : ""}`,
                        value: email,
                        onChange: (e) => setEmail(e.target.value),
                        placeholder: "Enter your email"
                    })
                ]),

                // Password
                h("div", { key: "pass", className: "mb-5" }, [
                    h("div", { key: "top", className: "flex justify-between items-center mb-1" }, [
                        h("label", { key: "l", className: "form-label mb-0" }, "Password"),
                        h("a", { key: "f", href: "index.php?page=reset-password", className: "text-[10px] font-semibold text-blue-600 hover:text-blue-800 uppercase tracking-wide" }, "Forgot?")
                    ]),
                    h("div", { key: "in", className: "input-relative" }, [
                        h("input", {
                            key: "inp",
                            type: showPassword ? "text" : "password",
                            className: `form-input pr-10 ${error && !password ? "border-red-500" : ""}`,
                            value: password,
                            onChange: (e) => setPassword(e.target.value),
                            placeholder: "Enter your password"
                        }),
                        h("button", {
                            key: "tog", type: "button",
                            onClick: () => setShowPassword(!showPassword),
                            className: "password-toggle"
                        }, h("i", { className: `fa-regular ${showPassword ? "fa-eye-slash" : "fa-eye"}` }))
                    ])
                ]),

                // Remember
                h("div", { key: "rem", className: "auth-checkbox" }, [
                    h("input", { key: "chk", id: "remember-me", type: "checkbox" }),
                    h("label", { key: "lbl", htmlFor: "remember-me" }, "Remember me")
                ]),

                // Button
                h("button", { key: "btn", type: "submit", className: "auth-submit-btn" }, "Login"),

                // Link
                h("div", { key: "lnk", className: "auth-link" }, [
                    "Don't have an account? ",
                    h("a", { key: "reg", href: "index.php?page=register", onClick: handleFlipToRegister, style:{cursor:"pointer"} }, "Sign Up")
                ])
            ])
        );
    }
    window.LoginForm = LoginForm;
})();
