/**
 * Created by usman.khan on 8/15/16.
 */
({
    myData : {},
    targets : {},
    initialize: function(view) {
        this._super('initialize', arguments);
        console.log('Views');
        self = this;
        app.api.call('GET', app.api.buildURL('Accounts/at_'), null, {
            success: function(data) {
                //console.log(data);

                self.myData = data;
                console.log('---');
                self.render();
            }
        });
    },

    render: function () {
        this._super('render');
        console.log('im in render...');
    }
})