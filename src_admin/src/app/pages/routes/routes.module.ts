import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';

import { UtilsModule } from './../../utils/index';
import { RoutesTableComponent } from './table/routesTable.component';

import { RoutesComponent } from './routes.component';
import { routing } from './routes.routing';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        UtilsModule,
        Ng2Bs3ModalModule
    ],
    exports: [
        RoutesComponent,
        RoutesTableComponent
    ],
    declarations: [
        RoutesComponent,
        RoutesTableComponent
    ],
    providers: []
})
export class RoutesModule {}
