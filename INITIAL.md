Feature name: Composer Dependncy Updater.

I need a Symfony console command that takes a GitHub PR number as argument and fetches a diff from the Pull Request. Based on the diff we need to identify changed module names that have also changed the transfer.xml file. This command should print out a comma separated list of changed modules.

I need a second console command that takes a comma separated list of module names, a package name that needs a version update, the expected version number to be added/update, and an optional description. The console command must use the AppFacade with a new method named "updateDependency". A new UpdateDependencyRequestTransfer must be added as well as a UpdateDependencyResponseTransfer.

Inside of the vertical slice for this feature we need to find out which version of the "package name" module is used in the composer.json file of the changed module. If it matches the expected number we do nothing. If it is not the expected version we need to continue.

When there is a requirement of the "package name" we need to update the version to the "expected version number".

When there is no requirement of the "package name" we need to add it and set the version to the "expected version number". In the case of the package name is "spryker/transfer" and it is not existing in the composer.json file we need to add or update a "dependency.json" file with the following content to the modules root:

```
{
    {
    "include": {
        "spryker/transfer": "Required to have the the description merging stratgey applied."
    }
}
```

When the "dependency.json" file already exists we need to update it.

The console application will be installed as a global composer dependency and thus must take the `getcwd()` function into account to work on the correct directory.

To identify the correct path to a composer.json file we have to use the path pattern `getcwd() . '/Bundles/' . $moduleName`.