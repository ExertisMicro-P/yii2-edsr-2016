//var sql_import_export = 'name LIKE "%Johnny%" AND (category = 2 OR in_stock = 1)';

var mongo_import_export = {
  "$and": [{
    "price": { "$lt": 10.25 }
  }, {
    "$or": [{
      "category": 2
    }, {
      "category": 1
    }]
  }]
}

$('#queryBuilder').queryBuilder({
  
  filters: [
  {
    id: 'ztorm_catalogue_cache.category',
    label: 'Category',
    type: 'string',
    input: 'checkbox',
    values: {
      'game': 'game',
      'software': 'software'
    },
    operators: ['in', 'not_in', 'equal', 'not_equal', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
  }, 
  {
    id: 'ztorm_catalogue_cache.format',
    label: 'Format',
    type: 'string',
    input: 'checkbox',
    values: {
      'pc': 'Pc',
      'mac': 'Mac',
      'multi': 'Multi',
      'pc_mac': 'PC Mac',
      'xbox360': 'Xbox 360',
      'xboxone': 'Xbox One',
    },
    operators: ['in', 'not_in', 'equal', 'not_equal', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
  }, 
  {
    id: 'ztorm_catalogue_cache.name',
    label: 'Name',
    type: 'string',
    input: 'text',
    operators: ['in', 'not_in', 'equal', 'not_equal', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
  }, 
  {
    id: 'ztorm_catalogue_cache.publisher',
    label: 'Publisher',
    type: 'string',
    input: 'checkbox',
    values: {
        '1C Publishing (IMG Publishing Ltd)': '1C Publishing (IMG Publishing Ltd)',
        'Anuman': 'Anuman',
        'Axis Game Factory': 'Axis Game Factory',
        'Daedalic Entertainment': 'Daedalic Entertainment',
        'Dovetail Games (RailSimulator.com)': 'Dovetail Games (RailSimulator.com)',
        'Dreamatrix Ltd': 'Dreamatrix Ltd',
        'Funcom Oslo AS': 'Funcom Oslo AS',
        'H2 Interactive Publishing': 'H2 Interactive Publishing',
        'Headup Games GMBH': 'Headup Games GMBH',
        'KISS': 'KISS',
        'Mastiff Games': 'Mastiff Games',
        'McAfee': 'McAfee',
        'Microsoft': 'Microsoft',
        'Plugin Digital': 'Plugin Digital',
        'Quadro Delta OY': 'Quadro Delta OY',
        'Raw Fury Games': 'Raw Fury Games',
        'SEGA': 'SEGA',
        'Taleworlds Entertainment': 'Taleworlds Entertainment',
        'Team 17 Digital Ltd': 'Team 17 Digital Ltd',
        'UIG Entertainment': 'UIG Entertainment'
    },
    operators: ['in', 'not_in', 'equal', 'not_equal', 'contains', 'not_contains', 'ends_with', 'not_ends_with']
  }]
});

$('#saveBtn').on('click', function() {
  var result = $('#queryBuilder').queryBuilder('getSQL', false);
  
  if (result.sql.length) {
      $('#accountrule-rulequery').val(result.sql);
  }
});
