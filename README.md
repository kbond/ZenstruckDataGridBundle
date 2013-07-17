# ZenstruckDataGridBundle

**NOTE:** This bundle is under heavy development, **use at your own risk**

Provides a sortable/filterable/paginated datagrid for your entities.

[![Screenshot][1]][2]

[View Demo][2]

## Full Default Configuration

```yaml
zenstruck_datagrid:

    # The default template to use when using the twig grid() function.
    default_template:     ZenstruckDataGridBundle:Twitter:blocks.html.twig
    grids:

        # Prototype
        name:

            # The entity (in the short notation) to create a grid for.
            entity:               ~ # Required, Example: AppBundle:Product

            # The service id for the generated grid. By default it is: "<bundle_prefix>.grid.<grid_name>".
            service_id:           ~

            # Customize the grid executor (must implement ExecutorInterface)
            executor_service:     ~

            # Whether or not to use a paginated grid.
            paginated:            true
            fields:

                # Prototype
                name:
                    label:                ~

                    # Set false to hide on display (can still be filtered/sorted)
                    visible:              true
                    filterable:           false
                    filter_value:         ~
                    sortable:             false
                    sort_direction:       ASC
                    format:               ~
                    align:                ~
                    default:              ~
```

## TODO

* sortable
* global filter
* nested entites filter/sort
* split out datagrid functionality into a separate library

[1]: https://lh5.googleusercontent.com/-iUd_BIQr-W4/Uea71hXUX8I/AAAAAAAAKJo/VOsEvnMbq50/w956-h296-no/datagrid.png
[2]: http://sandbox.zenstruck.com/articles