db.node.dropIndex( { nodeId: 1, siteId: 1, language: 1, version: 1 }, { unique: true } );
db.system.js.remove({_id : 'duplicateNode'});
