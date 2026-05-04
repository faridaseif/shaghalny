(function () {
  const { useState, useRef, useEffect, createElement: h } = React;

  function Header({ username = "Guest" }) {
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    const dropdownRef = useRef(null);

    const navItems = [
      { label: "Home", icon: "🏠", href: "index.php?page=home" },
      {
        label: "Jobs Map",
        icon: "📍",
        href: "index.php?controller=Map&action=map",
      },
      { label: "Social Feed", icon: "💬", href: "index.php?action=feed" },
      { label: "Messages", icon: "✉️", href: "index.php?controller=Message&action=inbox" },
      {
        label: "My Jobs",
        icon: "💼",
        href: "index.php?controller=Job&action=myJobs",
      },
      {
        label: "Post Job",
        icon: "➕",
        href: "index.php?controller=Job&action=create",
      },
      { label: "Support", icon: "❓", href: "index.php?action=support" },
    ];

    const dropdownItems = [
      {
        label: "View Profile",
        icon: "👤",
        href: "index.php?action=public_profile",
      },
      {
        label: "Edit Profile",
        icon: "✏️",
        href: "index.php?action=private_profile",
      },
      { label: "Support Center", icon: "ℹ️", href: "index.php?action=support" },
      {
        label: "About Us",
        icon: "🏢",
        href: "index.php?controller=Home&action=about",
      },
      {
        label: "Account Settings",
        icon: "⚙️",
        href: "index.php?action=settings",
      },
      { label: "Log out", icon: "🚪", href: "?action=logout" },
    ];

    // Close dropdown when clicking outside
    useEffect(() => {
      function handleClickOutside(event) {
        if (
          dropdownRef.current &&
          !dropdownRef.current.contains(event.target)
        ) {
          setIsDropdownOpen(false);
        }
      }
      document.addEventListener("mousedown", handleClickOutside);
      return () =>
        document.removeEventListener("mousedown", handleClickOutside);
    }, [dropdownRef]);

    return h(
      "header",
      { className: "nav-header" },
      h("div", { className: "header-content-wrapper" }, [
        // BRAND LOGO
        h(
          "a",
          { key: "brand", href: "index.php?page=home", className: "nav-brand" },
          [
            h("span", { key: "s1", className: "text-sha" }, "Sha"),
            h("span", { key: "s2", className: "char-g-container" }, "غ"),
            h("span", { key: "s3", className: "text-alny" }, "alny"),
          ]
        ),

        // NAV LINKS
        h(
          "ul",
          { key: "nav", className: "nav-links" },
          navItems.map((item) =>
            h(
              "li",
              { key: item.label },
              h("a", { href: item.href, className: "nav-link" }, [
                h("span", { key: "icon", className: "nav-icon" }, item.icon),
                h("span", { key: "txt" }, item.label),
              ])
            )
          )
        ),

        // RIGHT SIDE (Profile)
        h(
          "div",
          { key: "right", className: "header-right" },
          username === "Guest" || !username
            ? h(
                "div",
                {
                  key: "auth-btns",
                  className: "auth-buttons",
                  style: { display: "flex", gap: "10px", alignItems: "center" },
                },
                [
                  h(
                    "a",
                    {
                      key: "login",
                      href: "index.php?page=login",
                      className: "nav-link",
                      style: {
                        fontWeight: "bold",
                        marginRight: "10px",
                        cursor: "pointer",
                      },
                    },
                    "Sign In"
                  ),
                  h(
                    "a",
                    {
                      key: "register",
                      href: "index.php?page=register",
                      className: "btn-register",
                      style: {
                        backgroundColor: "#3b82f6",
                        color: "white",
                        padding: "8px 16px",
                        borderRadius: "6px",
                        textDecoration: "none",
                        fontWeight: "500",
                        transition: "background-color 0.2s",
                      },
                      onMouseOver: (e) =>
                        (e.target.style.backgroundColor = "#2563eb"),
                      onMouseOut: (e) =>
                        (e.target.style.backgroundColor = "#3b82f6"),
                    },
                    "Create Account"
                  ),
                ]
              )
            : [
                h("span", { key: "gr", className: "header-greeting" }, [
                  "Welcome, ",
                  h("b", { key: "b" }, username),
                  " ",
                  h(
                    "span",
                    { key: "w", role: "img", "aria-label": "waving" },
                    "👋"
                  ),
                ]),

                h(
                  "div",
                  {
                    key: "dd",
                    className: "profile-dropdown-container",
                    ref: dropdownRef,
                  },
                  [
                    // Avatar
                    h(
                      "div",
                      {
                        key: "avatar",
                        className: "header-avatar",
                        onClick: () => setIsDropdownOpen(!isDropdownOpen),
                        title: "Profile Menu",
                      },
                      [
                        username ? username[0].toUpperCase() : "G",
                        h("div", {
                          key: "dot",
                          className: "avatar-status-dot",
                        }),
                      ]
                    ),

                    // Dropdown
                    isDropdownOpen &&
                      h(
                        "div",
                        { key: "menu", className: "profile-dropdown-menu" },
                        [
                          h(
                            "div",
                            { key: "head", className: "dropdown-header-info" },
                            [
                              h(
                                "div",
                                { key: "av", className: "d-avatar" },
                                username ? username[0].toUpperCase() : "G"
                              ),
                              h("div", { key: "inf", className: "d-info" }, [
                                h(
                                  "span",
                                  { key: "n", className: "d-name" },
                                  username
                                ),
                                h(
                                  "span",
                                  { key: "r", className: "d-role" },
                                  "User"
                                ),
                              ]),
                            ]
                          ),
                          h("hr", {
                            key: "sep",
                            className: "dropdown-divider",
                          }),
                          dropdownItems.map((item, index) =>
                            h(
                              "a",
                              {
                                key: index,
                                href: item.href,
                                className: "dropdown-item",
                              },
                              [
                                h(
                                  "span",
                                  { key: "ic", className: "dropdown-icon" },
                                  item.icon
                                ),
                                item.label,
                              ]
                            )
                          ),
                        ]
                      ),
                  ]
                ),
              ]
        ),
      ])
    );
  }

  // Export globally
  window.Header = Header;
})();
