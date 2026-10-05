/**
 * Posyandu Smart - Real-time Live Synchronization Engine
 * Automatically syncs database changes across Admin & Kader sessions in real-time.
 */
(function () {
    let lastSyncTimestamp = Math.floor(Date.now() / 1000) - 5;
    let isSyncing = false;
    let syncInterval = null;
    let syncErrors = 0;

    // Toast Container
    let toastContainer = null;

    function initToastContainer() {
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'realtime-toast-container';
            toastContainer.style.cssText = `
                position: fixed;
                bottom: 24px;
                right: 24px;
                z-index: 99999;
                display: flex;
                flex-direction: column;
                gap: 10px;
                max-width: 380px;
                width: calc(100% - 48px);
                pointer-events: none;
            `;
            document.body.appendChild(toastContainer);
        }
    }

    function showToast(title, message, type = 'info') {
        initToastContainer();

        const toast = document.createElement('div');
        toast.style.cssText = `
            background: #ffffff;
            border-radius: 14px;
            padding: 14px 18px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid ${type === 'danger' ? '#fecaca' : '#bbf7d0'};
            border-left: 5px solid ${type === 'danger' ? '#dc2626' : '#16a34a'};
            pointer-events: auto;
            transform: translateX(120%);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-family: 'Inter', sans-serif;
        `;

        const icon = type === 'danger' ? '⚠️' : '🔔';
        toast.innerHTML = `
            <div style="font-size: 20px; line-height: 1; flex-shrink: 0; margin-top: 2px;">${icon}</div>
            <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 700; font-size: 13px; color: ${type === 'danger' ? '#991b1b' : '#166534'}; margin-bottom: 2px;">
                    ${title}
                </div>
                <div style="font-size: 12px; color: #475569; line-height: 1.4;">
                    ${message}
                </div>
                <div style="font-size: 10.5px; color: #94a3b8; margin-top: 4px;">
                    Baru saja &bull; Realtime Sync
                </div>
            </div>
            <button type="button" style="background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer; line-height: 1; padding: 0 4px;" onclick="this.parentElement.remove()">&times;</button>
        `;

        toastContainer.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
        });

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 400);
        }, 5000);
    }

    function animateValueChange(el, newValue) {
        if (!el) return;
        const currentText = el.textContent.trim();
        if (currentText !== String(newValue)) {
            el.textContent = newValue;
            el.style.transition = 'all 0.3s ease';
            el.style.transform = 'scale(1.15)';
            el.style.backgroundColor = 'rgba(16, 185, 129, 0.25)';
            el.style.borderRadius = '6px';
            el.style.padding = '0 4px';

            setTimeout(() => {
                el.style.transform = 'scale(1)';
                el.style.backgroundColor = 'transparent';
                el.style.padding = '0';
            }, 600);
        }
    }

    async function performRealtimeSync() {
        if (isSyncing) return;
        isSyncing = true;

        try {
            const url = new URL('/api/realtime-sync', window.location.origin);
            url.searchParams.set('last_sync', lastSyncTimestamp);

            const response = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                syncErrors++;
                isSyncing = false;
                return;
            }

            const data = await response.json();
            syncErrors = 0;

            if (data.status === 'success') {
                // Update Pulse Indicator
                const pulseEl = document.getElementById('realtime-sync-pulse');
                if (pulseEl) {
                    pulseEl.style.backgroundColor = '#10b981';
                }

                // Show new notifications
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(n => {
                        showToast(n.title, n.message, n.type);
                    });
                }

                // If DB updated or has changes, update DOM elements
                if (data.has_updates) {
                    // Update Admin Stats
                    if (data.admin_stats) {
                        const s = data.admin_stats;
                        animateValueChange(document.getElementById('rt-total-tapos'), s.total_tapos);
                        animateValueChange(document.getElementById('rt-total-balita'), s.total_balita);
                        animateValueChange(document.getElementById('rt-total-ibu-hamil'), s.total_ibu_hamil);
                        animateValueChange(document.getElementById('rt-balita-stunting'), s.balita_stunting_count);
                        animateValueChange(document.getElementById('rt-balita-pemantauan'), s.balita_pemantauan_count);
                        animateValueChange(document.getElementById('rt-ibu-hamil-high'), s.ibu_hamil_high_count);
                        animateValueChange(document.getElementById('rt-ibu-hamil-medium'), s.ibu_hamil_medium_count);
                        animateValueChange(document.getElementById('rt-pending-validation'), s.pending_validation_count);

                        // Update subtitle counts
                        const pemantauanSub = document.getElementById('rt-balita-pemantauan-sub');
                        if (pemantauanSub) pemantauanSub.textContent = `${s.balita_pemantauan_count} butuh pemantauan`;

                        const bumilMedSub = document.getElementById('rt-ibu-hamil-medium-sub');
                        if (bumilMedSub) bumilMedSub.textContent = `${s.ibu_hamil_medium_count} risiko sedang`;

                        // Update validation trend badge
                        const valTrend = document.getElementById('rt-pending-trend');
                        if (valTrend) {
                            valTrend.textContent = s.pending_validation_count > 0 ? 'Perlu Review' : 'Up to Date';
                            valTrend.className = `stat-trend ${s.pending_validation_count > 0 ? 'negative' : 'positive'}`;
                        }
                    }

                    // Update Kader Stats
                    if (data.kader_stats) {
                        const ks = data.kader_stats;
                        animateValueChange(document.getElementById('rt-kader-balita'), ks.balita_count);
                        animateValueChange(document.getElementById('rt-kader-ibu-hamil'), ks.ibu_hamil_count);
                        animateValueChange(document.getElementById('rt-kader-stunting'), ks.risiko_stunting_count);
                        animateValueChange(document.getElementById('rt-kader-followup'), ks.total_follow_up);
                    }

                    // Dispatch Global Custom Event
                    window.dispatchEvent(new CustomEvent('posyandu:sync', { detail: data }));
                }

                lastSyncTimestamp = data.server_time || Math.floor(Date.now() / 1000);
            }
        } catch (err) {
            console.debug('Realtime sync error:', err);
        } finally {
            isSyncing = false;
        }
    }

    // Initialize on DOM load
    document.addEventListener('DOMContentLoaded', function () {
        initToastContainer();

        // Run immediately
        performRealtimeSync();

        // Run every 2.5 seconds for instant reactive feedback
        syncInterval = setInterval(performRealtimeSync, 2500);
    });

    // Expose utility globally
    window.PosyanduRealtime = {
        syncNow: performRealtimeSync,
        showToast: showToast
    };
})();
