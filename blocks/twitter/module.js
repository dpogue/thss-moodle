/**
 * Twitter Helper
 * @author Darryl Pogue
 */
M.block_twitter = {
    /**
     * Initialize twitter system
     */
    init: function(Y, options) {
        var TwitterHelper = function(args) {
            TwitterHelper.superclass.constructor.apply(this, arguments);
        }
        TwitterHelper.NAME = "TWITTER";
        TwitterHelper.ATTRS = {
            options: {},
            lang: {}
        };
        Y.extend(TwitterHelper, Y.Base, {
            api: M.cfg.wwwroot+'/blocks/twitter/twitter.php',
            initializer: function(args) {
                var scope = this;
                this.account = args.account;
                
                this.load(args.page);
            },
            request: function(args, noloading) {
                var params = {};
                var scope = this;
                if (args['scope']) {
                    scope = args['scope'];
                }
                params['env']       = '';
                params['account']   = this.account;
 
                if (args['params']) {
                    for (i in args['params']) {
                        params[i] = args['params'][i];
                    }
                }
                var cfg = {
                    method: 'GET',
                    on: {
                        complete: function(id,o,p) {
                            if (!o) {
                                alert('IO FATAL');
                                return;
                            }
                            var data = Y.JSON.parse(o.responseText);
                            if (data.error) {
                                alert(data.error);
                                return false;
                            } else {
                                args.callback(id,data,p);
                                return true;
                            }
                        }
                    },
                    arguments: {
                        scope: scope
                    },
                    headers: {
                        'Content-Type': 'text/html; charset=UTF-8'
                    },
                    data: build_querystring(params)
                };
                Y.io(this.api, cfg);
                if (!noloading) {
                    this.wait();
                }
            },
            load: function(page) {
                var scope = this;
                var container = Y.one('#twitter_'+this.account);
                var params = {
                    'page': page,
                }
                this.request({
                    scope: scope,
                    params: params,
                    callback: function(id, ret, args) {
                        container.set('innerHTML', ret.status[0]);
                    }
                });
            },
            wait: function() {
                var container = Y.one('#twitter_'+this.account);
                container.set('innerHTML', '<img src="'+M.util.image_url('i/ajaxloader', 'core')+'">');
            }
        });

        new TwitterHelper(options);
    }
};
