(function() {

    window.seqr = {
        backUrl: ''
    };

    window.seqrStatusUpdated = function(data) {
        if (!data || ! data.status || data.status === 'ISSUED') return;
        window.location.href = window.seqr.backUrl;
    };
}());