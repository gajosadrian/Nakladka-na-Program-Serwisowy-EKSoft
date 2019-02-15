<footer id="page-footer" class="bg-body-light">
    <div class="content py-0">
        <div class="row font-size-sm">
            <div class="col-sm-6 order-sm-2 mb-1 mb-sm-0 text-center text-sm-right">
                Zaprojektowano przez: {{ author()->name }}
            </div>
            <div class="col-sm-6 order-sm-1 text-center text-sm-left">
                © <a class="font-w600" href="//agdev.pl" target="_blank">{{ author()->name }}</a> <span data-toggle="year-copy">{{ date('Y') }}</span>
            </div>
        </div>
    </div>
</footer>
