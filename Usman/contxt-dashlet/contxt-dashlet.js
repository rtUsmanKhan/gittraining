({
    extendsFrom: 'ListView',
    plugins: ['Dashlet'],

    initialize: function(options) {
        this._super('initialize', [options]);

        this.context.on('list:addtodash:fire', this.addtodash, this);
    },

    addtodash: function () {
        this.arr_Id = [];
        var collection2 = Backbone.Collection.extend({
        });
        var mycoll = new collection2();
        self = this;

        self.collection1 = new collection2();

        var ids = _.map(this.context.get('mass_collection').models, function(selected_model){
            self.arr_Id.push(selected_model.id);
        });

        _.each(self.arr_Id, function (value, key) {
            var OppBean = app.data.createBean('Opportunities', {id: value});
            OppBean.fetch();
            self.collection1.add(OppBean);
        });
        self.render();
    },
})
