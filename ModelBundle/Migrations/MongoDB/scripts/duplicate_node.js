db.node.createIndex( { nodeId: 1, siteId: 1, language: 1, version: 1 }, { unique: true } )
db.system.js.save(
    {
        _id : 'duplicateNode' ,
        value : function (data){
            var node = null
            while (1) {
                var cursor = db.node.find( { nodeId: data.nodeId, siteId: data.siteId, language: data.language }).sort( { version: -1 } ).limit(1);
                node = cursor.next()
                delete node._id;
                node.version = node.version + 1;
                node.status = data.status;
                var results = db.node.insert(node);
                if( results.hasWriteError() ) {
                    if( results.getWriteError().code == 11000)
                        continue;
                    else
                        print( 'unexpected error inserting data: ' + tojson( results ) );
                }
                break;
            }
            return node;
        }
    }
);