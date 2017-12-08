import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { Test } from './test.component';
import { routing } from './test.routing';

import { TableDemoComponent } from './testTable/testTable';

import { BtnViewer } from './btnViewer';
import { BootstrapBtnMessageService } from './btnViewer/bootstrapBtnMessage.service';
import { Ng2BootstrapModule } from 'ng2-bootstrap';

import { PaginationModule } from 'ng2-bootstrap';
import { Ng2TableModule } from 'ng2-table/ng2-table';

import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        Ng2TableModule,
        PaginationModule,
        routing,
        Ng2BootstrapModule,
        Ng2Bs3ModalModule
    ],
    declarations: [
        Test,
        BtnViewer,
        TableDemoComponent
    ],
    providers: [
        BootstrapBtnMessageService
    ]
})
export class TestModule {}
