(function($){
    $(function(){
        function updateJobView(job){ $('#wcu-result-pre').text(JSON.stringify(job,null,2)); $('#wcu-progress-val').text(job.progress||0); $('#wcu-result').show(); }
        function pollJob(){ $.post(WCU.ajax_url,{action:'wcu_get_job'}).done(function(r){ if(r&&r.success){ var job=r.data; if(job) updateJobView(job); if(job&&job.status==='running'){ setTimeout(pollJob,1000); } } }); }
        $('#wcu-clear-red').on('click',function(e){ e.preventDefault(); if(!confirm('Tüm önbelleği temizlemek istediğinize emin misiniz?')) return; $.post(WCU.ajax_url,{action:'wcu_start_job',nonce:WCU.nonce}).done(function(resp){ if(resp&&resp.success){ pollJob(); } else alert('Başlatılamadı: '+JSON.stringify(resp)); }); });
        $('.wcu-quick-btn').on('click',function(){ var act=$(this).data('action'); if(!confirm('İşlem: '+act+' - devam edilsin mi?')) return; var quick = act==='clear_all' ? 'clear_all' : act; $.post(WCU.ajax_url,{action:'wcu_quick_action', quick:quick, nonce:WCU.nonce}).done(function(r){ if(r&&r.success){ $('#wcu-result-pre').text(JSON.stringify(r.data,null,2)); $('#wcu-result').show(); } else alert('Hata: '+JSON.stringify(r)); }); });
        $.post(WCU.ajax_url,{action:'wcu_get_job'}).done(function(r){ if(r&&r.success&&r.data&&r.data.status==='running') pollJob(); });
    });
})(jQuery);
