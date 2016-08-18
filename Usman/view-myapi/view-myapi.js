/**
 * Created by usman.khan on 8/15/16.
 */
({
    /*
    * Variables which will be used to save data seperately
    * i.e from a single array to 3 different arrays
    * @var targets contains data related to targets
    * @var lead contains data related to leads
    * @var opp contains data related to Oppertunities
    */
    
    targets : {},
    lead : {},
    opportunity : {},

    /*
    * When instantiating a Backbone View, the initialize method will
    * call MyApi which is responsible for performing desired task.
    * Upon success it will get data returned by MyApi.
    * The data will be parsed by JSON parser to JS Object to seperate them
    * in different arrays.
    * @var trg We separate targets data into trg  using _.each
    * @var led contains seperated lead data using _.each
    * @var opp contains seperated opportunity data using _.each
    * The data is then saved to global arrays named targets, lead, opportunityetc using self in format of
    * id of each data is its index by iterating already seperated arrays trg, led and opp etc.
    * @method render is called with self to render it to view.
    */

    initialize: function(view) {
        this._super('initialize', arguments);
        self = this;
        app.api.call('GET', app.api.buildURL('Accounts/at_'), null, {
            success: function(data) {
                var response = JSON.parse(data);
                var trg = response.targets;
                var led = response.lead;
                var opp = response.opp;

                _.each(trg,function (value,key) {
                    self.targets[key] = value;
                });

                _.each(led,function (value,key) {
                    self.lead[key] = value;
                });

                _.each(opp,function (value,key) {
                    self.opportunity[key] = value;
                });
                    
                self.render();
            }
        });
    },

    render: function () {
        this._super('render');
    }
})
