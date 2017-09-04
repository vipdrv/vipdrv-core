import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';

import { UtilsModule } from './../../utils/index';
import { SitesTableComponent } from './table/sitesTable.component';

import { SitesComponent } from './sites.component';
import { routing } from './sites.routing';

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

    ],
    declarations: [
        SitesComponent,
        SitesTableComponent
    ],
    providers: []
})
export class SitesModule {}