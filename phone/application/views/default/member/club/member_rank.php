<div id="myRank" class="page out">
    <div class="header_top bar-nav">
        <a class="back-link-icon" href="#clubHome"></a>
        <h1 class="page-title-bar">我的等级</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">
        <div class="rank-hd">
            <div class="progress-bar">
                <i class="percentage" style="width: 62%;"></i>
                <img class="user-center-img" src="{$member['litpic']}" alt="" title="" />
                     {loop $progress $k $v}
                        {php} if(is_null($v)){continue;}{/php}
						<span class="num num-{php} echo ++$k;{/php} {if ($v['rank']==$grade['current'])}num-on{/if}">
							<em>lv.{$v['rank']}</em>
							<i class="core">{$v['begin']}</i>
						</span>
                     {/loop}
            </div>
            <p class="no">{$member['nickname']}</p>
            {if !is_null($grade['nextGrade'])}
            <p class="des">+{$grade['nextGrade']['poor']}&nbsp;即可成为{$grade['nextGrade']['name']}哦！</p>
            {/if}
        </div>

        <div class="rank-link">
            <ul class="clearfix">
                <li>
                    <a>
                        <i class="ico-1"></i>
                        <span>成长值</span>
                        <span>{$grade['jifen']}</span>
                    </a>
                </li>
                {if !is_null($grade['nextGrade'])}
                <li>
                    <a>
                        <i class="ico-2"></i>
                        <span>升级还需</span>
                        <span>{$grade['nextGrade']['poor']}</span>
                    </a>
                </li>
                {/if}
            </ul>
        </div>

        <div class="rank-list">
            <table clear_right=XLFwOs >
                <thead>
                <tr>
                    <th width="2.4rem">分值等级</th>
                    <th width="3.0rem">会员等级</th>
                    <th width="4.0rem">积分分值</th>
                </tr>
                </thead>
                <tbody>
                {php}$index=0;{/php}
                {loop $grade['grade'] $v}
                <tr>
                    <td>
                        <span class="level">Lv.{php} echo ++$index;{/php}</span>
                    </td>
                    <td>{$v['name']}</td>
                    <td>{$v['begin']}~{$v['end']}</td>
                </tr>
                {/loop}
                </tbody>
            </table>
            <p class="txt">会员共分{count($grade['grade'])}个等级，会员等级由“成长值”决定，经验值越高，会员等级越高。</p>
        </div>

    </div>

</div>
<!--我的等级-->