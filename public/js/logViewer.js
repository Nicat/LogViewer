var LogViewer = {
    /* Config object. Can change the route and limit of lines */
    config: {
        route: '/list',
        limit: 10,
        element: {
            result: '#lv-result',
            timer: '#lv-timer',
            begin: '#lv-begin',
            prev: '#lv-prev',
            next: '#lv-next',
            end: '#lv-end'
        }
    },
    _page: 0, //current page
    _path: null, // file path
    _action: null, // Action detector (prev or next)

    /* Store previous, current and next page. This is for not load api url next or prev page  */
    _storage: {
        prev: null,
        current: null,
        next: null
    },

    /* Get Result from api */
    getResult: function (last) {
        last = (typeof last !== 'undefined') ? last : false;

        var params = {
            path: this._path,
            page: this._page,
            limit: this.config.limit
        };
        if (last) {
            params.last = true;
        }

        var url = this.config.route + "?" + $.param(params, true);
        var result = null;
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (data) {
                result = data;
            },
            async: false // disable async
        });

        this.setTime(result.time);

        return result;
    },

    /* Next page */
    nextPage: function () {
        var increase;
        if (this._action === 'next') {
            increase = 1;
        } else {
            increase = 3;
            this._action = 'next';
        }
        this._page += increase;

        var newStorage = this._storage;
        var result = this.getResult();
        /* Set previous as current, current as next and next as new current */
        this.setStorage(newStorage.current, newStorage.next, result.current);
        this.setLines();
        this.pagination(true, true, result.current, true);

        return this;
    },

    /* Previous page */
    prevPage: function () {
        var decrease;
        if (this._action === 'prev') {
            decrease = 1;
        } else {
            decrease = 3;
            this._action = 'prev';
        }
        this._page -= decrease;

        if (this._page === -2) {
            this._page = 1;
            this._action = 'next';
        }

        else {
            var newStorage = this._storage;

            if (newStorage.prev === null) {
                this.setStorage(null, this.getResult().current, newStorage.current);
            } else {
                /* Set previous as new current, current as previous and next as current */
                this.setStorage(this.getResult().current, newStorage.prev, newStorage.current);
            }
            this.setLines();
            this.pagination(true, this._page !== -1, true, true);
        }


        return this;
    },

    /* Load or First Page */
    load: function (path, last) {
        last = (typeof last !== 'undefined') ? last : false;

        this._page = 0;
        this._path = path;
        var _result = this.getResult(last);

        this.setStorage(null, null, null);

        if (typeof _result.error === 'undefined') {
            this.setStorage(null, _result.current, _result.next);
        }
        this.setResult(_result);
        this._action = 'next';
        this._page++;

        return this;
    },

    /* Last Page */
    lastPage: function () {
        this.load(this._path, true);
        this.pagination(true, true, false, false);
        this._action = 'next';
    },

    /* Set Storage */
    setStorage: function (prev, current, next) {
        this._storage.prev = prev;
        this._storage.current = current;
        this._storage.next = next;
    },

    /* Set Result (Show Error alert or lines) */
    setResult: function (_result) {
        if (typeof _result.error !== 'undefined') {
            this.setError(_result.error)
        } else {
            this.setLines();
            this.pagination(true, _result.prev, _result.next, true);
        }

        if (typeof _result.page !== 'undefined') {
            this._page = _result.page;
        }
    },

    /* Set Error */
    setError: function (error) {
        console.error(error);
        $(this.config.element.result).html('<div class="alert alert-warning">' + error + '</div>');
        this.pagination(false, false, false, false);
    },

    /* Set lines */
    setLines: function () {
        var items = [];
        $.each(this._storage.current, function (key, val) {
            items.push('<div class="line">' + '<span class="line-number">' + val.line + '</span><span>' + val.text + '</span>' + '</div>');
        });

        $(this.config.element.result).html(items.join(""));
    },

    /* Config manager */
    setConfig: function (object) {
        if (object.hasOwnProperty('limit')) {
            this.config.limit = object.limit;
        }
        if (object.hasOwnProperty('route')) {
            this.config.route = object.route;
        }

        return this;
    },

    setTime: function (timer) {
        $(this.config.element.timer).text(timer);
    },

    /* Update Pagination button visibility */
    pagination: function (begin, prev, next, end) {
        $(this.config.element.begin).attr("data-click", begin === false ? false : true);
        $(this.config.element.prev).attr("data-click", prev === false ? false : true);
        $(this.config.element.next).attr("data-click", next === false ? false : true);
        $(this.config.element.end).attr("data-click", end === false ? false : true);
    }
}