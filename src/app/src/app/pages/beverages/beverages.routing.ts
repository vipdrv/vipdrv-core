import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { BeveragesComponent } from './beverages.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    // {
    //     path: '',
    //     component: BeveragesComponent,
    //     children: []
    // }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);