(function() {

    window.seqr = {
        id: '',
        timeout: 15,
        backUrl: ''
    };

    var seqrBasePath = window.location.pathname
            .substring(0, window.location.pathname.indexOf('/seqr/')) + '/seqr/confirmation';



    window.seqrStatusUpdated = function(data) {
        if (!data || ! data.status || data.status === 'ISSUED') return;
        window.location.href = window.seqr.backUrl;
    };
}());