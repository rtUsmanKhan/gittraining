/**
 * Case Count by Status example dashlet controller
 *
 * Controller logic watches the current collection on display and updates the
 * dashlet automatically whenever the current collection changes.
 *
 * This is a simple example of a dashlet for List Views in Sugar 7.x.
 *
 **/
({
    //This view uses the essential Dashlet plug-in
    plugins: ['Dashlet'],

    /*
     * Variables which will be used to save data seperately
     * i.e from a single array to 3 different arrays
     * @var targets contains data related to targets
     */

    opp : {},

    /* 
     * When instantiating a Backbone View, the initialize method will
     * call MyApi which is responsible for performing desired task.
     * Upon success it will get data returned by MyApi.
     * The data will be parsed by JSON parser to JS Object to seperate them
     * in different arrays.
     * @var trg We separate targets data into trg  using _.each
     * The data is then saved to global arrays named targets, lead, opportunity etc using self in format of
     * id of each data is its index by iterating already seperated arrays trg, led and opp etc.
     * @method render is called with self to render it to view.
     */

    initialize: function(options) {
        this._super('initialize', [options]);
      //  this.render();
        self = this;
        var P_id = this.model.get('id');
        this.context.on('button:save_button:click', this.save_button, this);
        app.api.call('GET', app.api.buildURL('Accounts/at_/' + P_id), null, {
            success: function (data) {
                var response = JSON.parse(data);
                var trg = response.opp;
                self.opp = {};
                _.each(trg, function (value, key) {
                    self.opp[key] = value;
                });
                self.render();
            }
        })
    },
    save_button : function () {
        //app.router.refresh();
        
	//app.router.refresh();
        //Backbone.history.loadUrl(Backbone.history.fragment);
        check = 3;

        /*var opp = {
            name: this.model.get('name')
        };
        //console.log(opp);

        rivets.configure({
            templateDelimiters : ['{{', '}}']
        });

        rivets.bind(
            document.querySelector('#dt'),
            {opp : opp}
        );*/
        document.getElementById('userProfile');
        $('#userProfile').text(this.model.get('name'));
	
	console.log('Refreshed');
    },
    
})
