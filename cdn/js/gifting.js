var gifting = 
{
    api_url: "",
    cdn_url: "",
    token: "",
    items: {},
    on_complete_callback: "",
    on_accept: "",
    on_i18n: "",
    gifts: 0,
    maxSelection: 50,
    gifting_message: '$facebookGiftingMessage',
    elements: {},
    callbacks: {},

    init: function (options)
    {
        this.api_url = options['api_url'];
        this.cdn_url = options['cdn_url'];
        this.token = options['token'];
        this.load_items (options['items_url']);
        this.on_complete_callback = options['callbacks']['complete'];
        this.on_accept = options['callbacks']['accept'],
        this.on_remove = options['callbacks']['remove'],
        this.on_i18n = options['callbacks']['i18n'];

        for (var element in options['elements'])
        {
            this.elements[element] = options['elements'][element];
        }

        TDFriendSelector.init({debug: true});
    },

    load_items: function (url)
    {
        var scope = this;
        $.ajax
        (
            {
                url:  url
            }
        ).done
        (
            function (reply) 
            {
                scope.items = reply;
            }
        );
    },

    render_item: function (item_id, item)
    {
        var item_name = this.on_i18n (item['name']);
        var item_url = this.cdn_url + '/' + item['url'];

        // TODO: Cache results from on_1i8n()
        var buf = 
            '<img align="center" src="' + item_url + '"/>' + 
            '<br/>' + 
            '<span class="gift_item_name">' + 
            item_name + 
            '</span>';
        return buf;
    },

    render_item_send: function (item_id, item)
    {
        var item_name = this.on_i18n (item['name']);
        var item_url = this.cdn_url + '/' + item['url'];

        // TODO: Cache results from on_1i8n()
        var buf = 
            '<img class="gift_image_grid_holder" align="center" src="' + item_url + '"/>' + 
            '<br/>' + 
            '<span class="gift_item_name">' + 
            item_name + 
            '<br/>' + 
            '<a href="#top" title="' + item_id + '" class="send_gift_button">Send</a>' + 
            '</span>';
            
        return buf;
    },

    get_unique_facebook_ids_from_gifts: function (gifts)
    {
        var facebook_ids = {};
        for (var i = 0; i < gifts.length; i++)
        {
            facebook_ids[gifts[i][0]] = true;
        }
        return Object.keys (facebook_ids);
    },

    make_user_index: function (user_info)
    {
        var user_index = {};
        for (var i = 0; i < user_info.length; i++)
        {
            user_index[user_info[i]['uid']] = user_info[i];
        }
        return user_index;
    },

    send_gifts: function()
    {
        var item_id, item;
        var grid = [];
        
        for (item_id in this.items['items'])
        {
            item = this.items['items'][item_id];
            grid.push (this.render_item_send (item_id, item));
        }
        
        var buf = '<div class="gift_grid_wrapper"><div class="gift_grid">';

        $.each
        (
            grid, 
            function (i, val)
            {
                buf += '<span class="gift_grid_square">' + val + '</span>';
            }
        );

        buf += '</div></div>';

        $("#" + this.elements['list_element']).html (buf).show();

        var scope = this;

        $(document).on("click", "a.send_gift_button", function (e) { scope.send ($(this), e, scope); });
    },

    render: function (gifted)
    {
        if (! gifted.length)
        {
            return this.send_gifts();
        }

        var ids = this.get_unique_facebook_ids_from_gifts (gifted);
        var scope = this;

        FB.api
        (
            '/fql?q=' + encodeURIComponent ('select uid, name FROM user WHERE uid IN (' + ids.join (',') + ')'),
            function (response)
            {
                scope.on_render_ready (gifted, response['data']);
            }
        );
    },

    on_render_ready: function (gifted, user_info)
    {
        var scope = this;
        var user_index = this.make_user_index (user_info);
        this.gifts = gifted.length;

        var html = 
            '<br/><center><table id="gifts_received" cellpadding="5" cellspacing="5">' + 
            '<tr>' + 
            '<td align="center">' + 
            "FROM" + 
            '</td>' + 
            '<td align="center">' + 
            "ITEM" + 
            '</td>' + 
            '<td align="center">' + 
            "DATE" + 
            '</td>' + 
            '<td align="center">' + 
            "SENT" + 
            '</td>' + 
            '</tr>';

        var facebook_id, item, item_id, create_date, request_id;
        var colours = [ "d9d9d9", "efefef" ];
        var colour = false;
        var color;
        var item_html;
        var available = 0;

        for (var i = 0; i < gifted.length; i++)
        {
            item_id = gifted[i][1];
            request_id = gifted[i][3];
            
            if (! this.items['items'].hasOwnProperty (item_id))
            {
                console.log ("Missing item #" + item_id);
                this.on_remove (request_id);
                continue;
            }

            facebook_id = gifted[i][0];

            if (! user_index[facebook_id])
            {
                console.log ("Missing Facebook user #" + facebook_id);
                this.on_remove (request_id);
                continue;
            }

            ++available;
            item = this.items['items'][item_id];

            create_date = gifted[i][2];
            colour = ! colour;
            color = colours[colour ? 1 : 0];
            item_html = this.render_item (item_id, item);

            html += 
                '<tr id="request' + request_id + '" bgcolor="#' + color + '">' + 
                '<td align="center">' +
                '<img align="center" src="https://graph.facebook.com/' + facebook_id + '/picture"><br/>' + 
                '<span class="gift_facebook_name">' + user_index[facebook_id]['name'] + '</span>' + 
                '</td>' + 
                '<td align=center">' + 
                item_html + 
                '</td>' + 
                '<td align=center">' + 
                create_date + 
                '</td>' + 
                '<td align=center">' + 
                '<a title="' + request_id + '" class="gift_accept" href="#">ACCEPT</a>' + 
                '</td>' + 
                '</tr>';

        }
        html += '</table></center></br>';

        if (! available)
        {
            return this.send_gifts();
        }


        $("#" + scope.elements['list_element']).html (html).show();

        $(".gift_accept").click
        (
            function ()
            {
                var request_id = $(this).attr ('title');
                $("#request" + request_id).hide();
                scope.on_accept (request_id);
                if (--scope.gifts <= 0)
                {
                    scope.send_gifts();
                    $("#gifts_received").hide();
                    $("#" + scope.elements['list_element']).show();
                }
                return false;
            }
        );
    },

    send: function (button, e, scope)
    {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        e.preventDefault();
        var current_item_id = button.attr('title');
        var message = scope.on_i18n (scope.gifting_message);
        var selector1 = TDFriendSelector.newInstance
        (
            {
                maxSelection: scope.maxSelection,
                autoDeselection: false, 
                callbackSubmit: function(selectedFriendIds) 
                {
                    FB.ui
                    (
                        {
                            method: 'apprequests',
                            message: message,
                            to: selectedFriendIds.join(",")
                        }, 
                        function (response)
                        {
                            if (response.hasOwnProperty ('request'))
                            {
                                $.ajax
                                (
                                    {
                                        url: scope.api_url + "/gifts/send/" + encodeURIComponent (response['request']) + "/" + encodeURIComponent (current_item_id) + "?token=" + encodeURIComponent (token),
                                        type: "POST",
                                        data: { friends: selectedFriendIds }
                                    }
                                ).done
                                (
                                    function (whatever)
                                    {
                                        scope.on_complete_callback();
                                    }
                                );
                            }
                            else
                            {
                                scope.on_complete_callback();
                            }
                        }
                    );
                }
            }
        );
        selector1.showFriendSelector();
    }
};
