import { Routes, RouterModule }  from '@angular/router';

import { Test } from './test.component';
import { ModuleWithProviders } from '@angular/core';

// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: Test,
        children: [
            //{ path: 'treeview', component: TreeViewComponent }
        ]
    }
];

export const routing: ModuleWithProviders = RouterModule.forChild(routes);