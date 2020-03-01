import { Routes, RouterModule }  from '@angular/router';

import { LoginComponent } from './login.component';
import { ModuleWithProviders } from '@angular/core';

// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: LoginComponent,
        children: [
            //{ path: 'treeview', component: TreeViewComponent }
        ]
    }
];

export const routing: ModuleWithProviders = RouterModule.forChild(routes);