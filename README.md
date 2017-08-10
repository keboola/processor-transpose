# processor-transpose

[![Build Status](https://travis-ci.org/keboola/processor-transpose.svg?branch=master)](https://travis-ci.org/keboola/processor-transpose)

Transforms table data from input CSV file into key-value pairs. 

Example input:

   |01/2016|01/2016|02/2016|02/2016|03/2016|03/2016
---|---|---|---|---|---|---
Manager|Margin|Bonus|Margin|Bonus|Margin|Bonus
Alice|855000|9000|855000|9500|877500|9500
Bob|475000|7000|570000|7500|585000|7500
Eve|142500|3500|475000|6500|585000|7500

Example output (using sample configuration below):

manager|month|key|value
---|---|---|---
Alice|01/2016|Margin|855000
Alice|01/2016|Bonus|9000
Alice|02/2016|Margin|855000
Alice|02/2016|Bonus|9500
Alice|03/2016|Margin|877500
Alice|03/2016|Bonus|9500
Bop|01/2016|Margin|475000
Bop|01/2016|Bonus|7000
Bop|02/2016|Margin|570000
Bop|02/2016|Bonus|7500
Bop|03/2016|Margin|585000
Bop|03/2016|Bonus|7500
Eve|01/2016|Margin|142500
Eve|01/2016|Bonus|3500
Eve|02/2016|Margin|475000
Eve|02/2016|Bonus|6500
Eve|03/2016|Margin|585000
Eve|03/2016|Bonus|7500



## Development
 
Clone this repository and init the workspace with following commands:

- `docker-compose build`

### TDD 

 - Edit the code
 - Run `docker-compose run --rm processor-transpose-tests` or you can filter tests running `docker-compose run --rm processor-transpose-tests php vendor/bin/phpunit --filter testName`
 - Repeat
 
# Integration
 - Build is started after push on [Travis CI](https://travis-ci.org/keboola/processor-transpose)
 - [Build steps](https://github.com/keboola/processor-transpose/blob/master/.travis.yml)
   - build image
   - execute tests against new image
   - publish image to AWS ECR and update tag in Keboola Developer Portal
   
# Usage

## Sample configuration

```
{  
    "definition": {
        "component": "keboola.processor.transpose"
    },
    "parameters": {
        {
            "filename": "asdfghjkl_1234_out.csv"
            "header_rows_count": 2,
            "header_column_names":["manager"],
            "header_transpose_row": 1,
            "header_transpose_column_name": "month",
            "header_sanitize": true,
            "transpose_from_column": 2                            
        } 
    }
}
```

## Parameters

### filename

Name of the file to process.

### header_rows_count

Number of rows that forms a header.

### header_column_names

Overrides output header. 
When using transposition, the "key" and "value" columns will be appended 
as well as column defined in `header_transpose_column_name`.

### header_transpose_row

The number of header row, that will be transposed. 
In our example this is the first row of the header, containing "months".
 
### header_transpose_column_name

How to name the row, specified in `header_transpose_row`, after being transposed to a column

### header_sanitize

Remove special characters from header if set to `true`, which is the default value.
 
### transpose_from_column

The number of column from which to transpose the data.
In our example, we transform from the second column.

