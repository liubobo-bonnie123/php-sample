var toggler =
{
    elements: {},

    add_html_element: function (element)
    {
        this.elements[element] = 'html';
    },

    add_flash_element: function (element, type)
    {
        this.elements[element] = 'flash';
    },

    hideall: function()
    {
        for (var element in this.elements)
        {
            this.hide (element);
        }               
    },

    hide: function (element)
    {
        if (! this.elements.hasOwnProperty (element))
        {
            return;
        }
        switch (this.elements[element])
        {
            case 'flash':
                $('#' + element).width ('0%');
                $('#' + element).height ('0%');
                break;
            case 'html':
                $("#" + element).hide();
                break;
        }
    },

    show: function (element)
    {
        if (! this.elements.hasOwnProperty (element))
        {
            return;
        }
        if (this.elements[element] == 'flash')
        {
            $('#' + element).width ('100%');
            $('#' + element).height ('100%');
        }
        else if (this.elements[element] == 'html')
        {
            $("#" + element).show();
        }
    }
};
