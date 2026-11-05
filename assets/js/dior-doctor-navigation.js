/**
 * Dior Medical — Doctor Dashboard Navigation Safety Controller
 * Provides isolated, rock-solid sidebar navigation with zero dependencies.
 * Ensures Doctor Dashboard tabs ALWAYS switch smoothly even if other widgets fail.
 */
(function () {
    'use strict';

    // Normalize tab ID aliases
    function normalizeTabId(tabId) {
        if (!tabId) return 'doc-overview';
        var t = String(tabId).trim();
        if (t === 'feedback' || t === 'doc-feedback' || t === 'patient-review' || t === 'reviews') {
            return 'doc-patient-review';
        }
        if (t === 'notifications' || t === 'doc-notifications') {
            return 'doc-notifications-system';
        }
        if (t === 'documents' || t === 'doc-documents') {
            return 'doc-documents-upload';
        }
        if (t === 'patients' || t === 'doc-patients') {
            return 'doc-patients-all';
        }
        if (t === 'appointments') {
            return 'doc-appointments';
        }
        if (t === 'settings') {
            return 'doc-settings';
        }
        return t;
    }

    // Global submenu toggle
    window.diorToggleSubmenu = function (e, menuId) {
        if (e && e.preventDefault) e.preventDefault();
        var targetMenu = document.getElementById(menuId);
        if (!targetMenu) return;

        var isAlreadyOpen = targetMenu.classList.contains('active') || targetMenu.classList.contains('open');

        document.querySelectorAll('#dior-doc-sidebar .dior-nav-item.has-submenu').forEach(function (item) {
            if (item.id !== menuId) {
                item.classList.remove('active', 'open');
                var icon = item.querySelector('.submenu-icon');
                if (icon) {
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                }
            }
        });

        if (isAlreadyOpen) {
            targetMenu.classList.remove('active', 'open');
            var icon = targetMenu.querySelector('.submenu-icon');
            if (icon) {
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        } else {
            targetMenu.classList.add('active', 'open');
            var icon = targetMenu.querySelector('.submenu-icon');
            if (icon) {
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            }
        }
    };

    /* ------------------------------------------------------------------
       Mobile offcanvas drawer
       Single source of truth for the drawer state. Every entry point
       (hamburger, X button, backdrop tap, outside tap, Esc, tab change)
       funnels through diorDocSetDrawer() so the sidebar, the backdrop and
       the body scroll lock can never drift out of sync.
       ------------------------------------------------------------------ */
    var DOC_DRAWER_MQ = '(max-width: 1024.98px)';

    function diorDocDrawerIsMobile() {
        if (window.matchMedia) {
            return window.matchMedia(DOC_DRAWER_MQ).matches;
        }
        return window.innerWidth <= 1024;
    }

    function diorDocDrawerEls() {
        return {
            sidebar: document.getElementById('dior-doc-sidebar'),
            backdrop: document.getElementById('dior-doc-backdrop'),
            toggle: document.getElementById('dior-doc-menu-toggle')
        };
    }

    function diorDocSyncDrawerUi(isOpen) {
        var els = diorDocDrawerEls();
        var isMobile = diorDocDrawerIsMobile();
        if (els.sidebar) {
            els.sidebar.classList.toggle('mobile-open', isOpen);
            // Never hide the permanent desktop rail from assistive tech.
            if (isMobile) {
                els.sidebar.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            } else {
                els.sidebar.removeAttribute('aria-hidden');
            }
        }
        if (els.backdrop) {
            // Both hooks are used across the stylesheets (`.active` in the
            // drawer CSS, `.open` in the legacy rules) — keep them together.
            els.backdrop.classList.toggle('active', isOpen);
            els.backdrop.classList.toggle('open', isOpen);
            els.backdrop.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        }
        if (els.toggle) {
            els.toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }
        document.body.classList.toggle('dior-drawer-locked', !!isOpen && diorDocDrawerIsMobile());
    }

    window.diorDocSetDrawer = function (open) {
        var els = diorDocDrawerEls();
        if (!els.sidebar) return;
        var isOpen = !!open;
        if (isOpen && !diorDocDrawerIsMobile()) {
            // Desktop keeps the permanent rail: no drawer, no backdrop.
            isOpen = false;
        }
        diorDocSyncDrawerUi(isOpen);
    };

    window.diorDocIsDrawerOpen = function () {
        var els = diorDocDrawerEls();
        return !!(els.sidebar && els.sidebar.classList.contains('mobile-open'));
    };

    // Mobile drawer close helper
    window.diorDocCloseMobile = function () {
        window.diorDocSetDrawer(false);
    };

    // Mobile drawer open helper (toggles when called from the hamburger)
    window.diorDocOpenMobile = function (e) {
        if (e && e.preventDefault) e.preventDefault();
        if (window.innerWidth > 1024) {
            // Desktop: the sidebar is always visible, so use the toggle as a
            // collapse/expand control instead of opening an offcanvas drawer.
            var wrap = document.querySelector('.dior-doctor-wrap .dior-app') ||
                document.querySelector('.dior-doctor-wrap') ||
                document.querySelector('.dior-app');
            if (wrap) {
                wrap.classList.toggle('dior-sidebar-collapsed');
            } else {
                var sb = diorDocDrawerEls().sidebar;
                if (sb) sb.classList.toggle('collapsed');
            }
            return;
        }
        window.diorDocSetDrawer(!window.diorDocIsDrawerOpen());
    };

    // Main tab switcher
    window.diorDocSwitchTab = function (tabId, updateUrl) {
        var app = document.getElementById('dior-doctor-app');
        if (!tabId) return false;
        var normalizedId = normalizeTabId(tabId);

        // Find matching section panel
        var activePanel = document.getElementById('tab-' + normalizedId);
        if (!activePanel && app) {
            // Check if standard tab without prefix exists
            activePanel = document.getElementById(normalizedId);
        }
        if (!activePanel) {
            console.warn('[Dior Navigation] Panel not found for tab:', tabId, '(normalized: ' + normalizedId + ')');
            return false;
        }

        // Close notification dropdown if open
        var notifDropdown = document.getElementById('dior-doc-notif-dd');
        if (notifDropdown) {
            notifDropdown.classList.remove('open', 'active');
        }

        // Update nav buttons
        var buttons = document.querySelectorAll('#dior-doc-sidebar .dior-nav-btn');
        buttons.forEach(function (b) {
            b.classList.remove('active');
        });

        var activeBtn = document.querySelector('#dior-doc-sidebar .dior-nav-btn[data-tab="' + normalizedId + '"]');
        if (activeBtn) {
            activeBtn.classList.add('active');

            // Strict Accordion: Expand parent submenu if inside one, close all others
            var parentSubmenu = activeBtn.closest('.dior-nav-item.has-submenu');
            document.querySelectorAll('#dior-doc-sidebar .dior-nav-item.has-submenu').forEach(function(sub) {
                if (parentSubmenu && sub === parentSubmenu) {
                    sub.classList.add('active', 'open');
                    var subIcon = sub.querySelector('.submenu-icon');
                    if (subIcon) {
                        subIcon.classList.remove('fa-plus');
                        subIcon.classList.add('fa-minus');
                    }
                } else {
                    sub.classList.remove('active', 'open');
                    var subIcon = sub.querySelector('.submenu-icon');
                    if (subIcon) {
                        subIcon.classList.remove('fa-minus');
                        subIcon.classList.add('fa-plus');
                    }
                }
            });

            // Dismiss badge with transition if present
            var badge = activeBtn.querySelector('.nav-count-badge');
            if (badge) {
                badge.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                badge.style.opacity = '0';
                badge.style.transform = 'scale(0.5)';
                setTimeout(function () {
                    badge.style.display = 'none';
                }, 250);
            }
        }

        // Update tab panels
        var panels = document.querySelectorAll('#dior-doc-content > .dior-tab-panel');
        panels.forEach(function (panel) {
            if (panel === activePanel) {
                panel.classList.add('active');
                panel.removeAttribute('hidden');
                panel.style.removeProperty('display');
                panel.style.display = 'block';
            } else {
                panel.classList.remove('active');
                panel.setAttribute('hidden', 'hidden');
                panel.style.display = 'none';
            }
        });

        // Update URL hash
        if (updateUrl !== false && window.history && window.history.replaceState) {
            try {
                window.history.replaceState(null, '', '#tab=' + encodeURIComponent(normalizedId));
            } catch (e) { }
        }

        // Refresh table pagination if table exists in active panel
        if (window.diorDoctorInitTablePagination) {
            activePanel.querySelectorAll('.dior-doc-table, .dior-table, .dior-clean-table').forEach(function (table) {
                if (table._diorRenderPage) {
                    table._diorRenderPage(1);
                } else {
                    window.diorDoctorInitTablePagination(table, 5);
                }
            });
        }

        // Close mobile drawer on selection
        window.diorDocCloseMobile();

        return true;
    };

    function boot() {
        var app = document.getElementById('dior-doctor-app');
        if (!app || app.__diorDoctorNavigationReady) return;
        app.__diorDoctorNavigationReady = true;

        // Capture-phase event delegation for rock-solid click response
        app.addEventListener('click', function (event) {
            // Check if clicking a tab switch button
            var tabBtn = event.target.closest('#dior-doc-sidebar .dior-nav-btn[data-tab]');
            if (tabBtn) {
                var tab = tabBtn.getAttribute('data-tab');
                if (tab) {
                    event.preventDefault();
                    event.stopPropagation();
                    window.diorDocSwitchTab(tab, true);
                    return;
                }
            }

            // Check if clicking a parent submenu toggle button
            var parentMenuBtn = event.target.closest('#dior-doc-sidebar .dior-nav-item.has-submenu > .dior-nav-btn');
            if (parentMenuBtn && !parentMenuBtn.getAttribute('data-tab')) {
                var parentItem = parentMenuBtn.closest('.dior-nav-item.has-submenu');
                if (parentItem && parentItem.id) {
                    event.preventDefault();
                    event.stopPropagation();
                    window.diorToggleSubmenu(event, parentItem.id);
                    return;
                }
            }
        }, true);

        // Sync tab from URL hash on load
        function syncTabFromUrl(updateUrl) {
            var hashMatch = window.location.hash.match(/^#tab=([^&]+)/);
            var requestedTab = hashMatch ? decodeURIComponent(hashMatch[1]) : 'doc-overview';
            requestedTab = normalizeTabId(requestedTab);

            var panel = document.getElementById('tab-' + requestedTab);
            var tabToLoad = panel ? requestedTab : 'doc-overview';
            window.diorDocSwitchTab(tabToLoad, updateUrl);
        }

        window.addEventListener('hashchange', function () {
            syncTabFromUrl(false);
        });

        // Initialize immediately
        syncTabFromUrl(false);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }

    /* ------------------------------------------------------------------
       Drawer dismissal: outside tap, backdrop tap, Esc and resize.
       Registered in the capture-free bubbling phase so nav buttons keep
       working; listeners are delegated so they survive re-renders.
       ------------------------------------------------------------------ */
    function installDrawerDismissHandlers() {
        if (window.__diorDocDrawerDismissReady) return;
        window.__diorDocDrawerDismissReady = true;

        function isDrawerOpen() {
            var sidebar = document.getElementById('dior-doc-sidebar');
            return !!(sidebar && sidebar.classList.contains('mobile-open'));
        }

        document.addEventListener('click', function (event) {
            if (!isDrawerOpen()) return;
            var sidebar = document.getElementById('dior-doc-sidebar');
            var target = event.target;

            // Clicks inside the drawer (or its children) stay untouched.
            if (sidebar && target && target.closest && sidebar.contains(target)) return;
            // Do not instantly re-close when the hamburger itself opened it.
            if (target && target.closest && target.closest('.dior-mobile-menu-toggle, #dior-doc-menu-toggle')) return;

            window.diorDocCloseMobile();
        }, false);

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape' && event.key !== 'Esc') return;
            if (!isDrawerOpen()) return;
            window.diorDocCloseMobile();
            var toggle = document.getElementById('dior-doc-menu-toggle');
            if (toggle && toggle.focus) toggle.focus();
        }, false);

        window.addEventListener('resize', function () {
            if (window.innerWidth > 1024 && isDrawerOpen()) {
                window.diorDocCloseMobile();
            }
        });
    }

    installDrawerDismissHandlers();
})();
