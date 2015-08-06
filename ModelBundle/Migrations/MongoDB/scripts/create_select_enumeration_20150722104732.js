db.system.js.remove({_id : 'selectEnumeration'});
db.system.js.save(
    {
        _id : 'selectEnumeration' ,
        value : function (data){
            var collection_keys_name = data.collection + '_keys_' + (new Date()).getTime();
            var enumeration = null;
            var criterias = {'$or' : []};
            db[data.collection].mapReduce(
                function() {
                    for (var key in eval('this.' + preColumn)) { emit(preColumn + '.' + key + postColumn, null); }
                },
                function(key, stuff) { return null; },
                {
                    'out': collection_keys_name,
                    'scope': {
                        'preColumn': data.column.replace(/(.*)\.\*(.*)/, '$1'),
                        'postColumn': data.column.replace(/(.*)\.\*(.*)/, '$2')
                    }
                }
            );
            enumeration = db[collection_keys_name].distinct('_id');
            db[collection_keys_name].drop();
            for(var i in enumeration) {
                criteria = {};
                criteria[enumeration[i]] = data.search;
                criterias['$or'].push(criteria);
            }
            return db[data.collection].find(criterias);
        }
    }
);
//db.loadServerScripts();
//selectEnumeration({collection: 'content_type', column: 'names.*.value', search: 'News'});
//selectEnumeration({collection: 'content_type', column: 'names.fr.*', search: 'News'});