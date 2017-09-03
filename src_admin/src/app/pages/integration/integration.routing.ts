import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { IntegrationComponent } from './integration.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: IntegrationComponent,
        children: []
    }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);