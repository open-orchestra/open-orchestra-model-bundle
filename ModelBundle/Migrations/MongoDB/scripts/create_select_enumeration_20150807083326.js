db.system.js.save(
    {
        _id : 'selectEnumeration' ,
        value : function (data){
            var parseCriteria = function(criteria) {
                for(var i in criteria) {
                    if(i.indexOf('*') !== -1) {
                        criteria['$or'] = generateCriteria(i, criteria[i]);
                        delete criteria[i];
                    }
                    if(typeof(criteria[i]) !== 'string') {
                        parseCriteria(criteria[i]);
                    }
                }
                return criteria;
            }
            var generateCriteria = function(key, value) {
                var collection_keys_name = data.collection + '_keys_' + (new Date()).getTime();
                var enumeration = null;
                db[data.collection].mapReduce(
                    function() {
                        for (var key in eval('this.' + preColumn)) { emit(preColumn + '.' + key + postColumn, null); }
                    },
                    function(key, stuff) { return null; },
                    {
                        'out': collection_keys_name,
                        'scope': {
                            'preColumn': key.replace(/(.*)\.\*(.*)/, '$1'),
                            'postColumn': key.replace(/(.*)\.\*(.*)/, '$2')
                        }
                    }
               );
               enumeration = db[collection_keys_name].distinct('_id');
               db[collection_keys_name].drop();
               criterias = [];
               for(var i in enumeration) {
                    criteria = {};
                    criteria[enumeration[i]] = value;
                    criterias.push(criteria);
               }
               return criterias;
            }
            return db[data.collection].find(parseCriteria(data.criteria));
        }
    }
);
