SET QUOTED_IDENTIFIER ON 
GO
SET ANSI_NULLS ON 
GO

ALTER trigger trig_locations 
on dbo.tblocations 
for insert 
as 

-- deklarasi
declare @tid bigint, @license varchar(10),@driver varchar(20),@driverhp varchar(20),@lat float,
@lon float,@speed float,@course smallint,@time datetime,@groupID tinyint,@status int, @wifi bit,
@rectime datetime,@fix bit,@oilLevel float,@inputpower char(2),@customerID int, @time_last datetime

-- ambil data yang dimasukan
select @tid=tid,@lat=lat,@lon=lon,@speed=speed,@course=course,@time=[time],
@status=status,@rectime=receivetime,@fix=fixGPS,@oilLevel=oilLevel,
@inputpower=[input power] from inserted

-- ambil data referensi
select @license=license,@driver=driver,@driverHP=driverHP,@groupID=groupID,@customerID=customerID 
from tblicenseTran where tid=@tid and @time between startTime and endTime 

set @time_last=(select [time] from tblatestlocation where tid=@tid)
set @wifi=(@status&0x1000000)


if(@fix=1) --not fix, not insert.
begin
      --if(not exists (select tid from tblatestlocation where tid=@tid))
      if(@time_last is null) --should insert.
      begin
        insert tblatestlocation (license,tid,driver,driverHP,latitude,longitude,speed,course,
        	[time],groupID,
        	alarm,panic,
        	[ignition on],
        	receiveTime,oilLevel,[input power],customerID,lastRcvTime) 
        values (isnull(@license,'N.A.'),@tid,isnull(@driver,'N.A.'),isnull(@driverHP,'N.A.'),@lat,@lon,@speed,
        	@course,@time,isnull(@groupID,1),0,0,(@status&0x080000),
        	@rectime,@oilLevel,@inputpower,@customerID,@rectime)	
      end
      else
      begin
          --should compare time 
          if(@time_last<@time)
          begin
            update tblatestlocation 
            set license=isnull(@license,'N.A.'),driver=isnull(@driver,'N.A.'),driverhp=isnull(@driverhp,'N.A.'),
            latitude=@lat,longitude=@lon,customerID=@customerID,
            speed=@speed,course=@course,[time]=@time,
            alarm=0,
            panic=0,[ignition on]=(@status&0x080000),
            groupID=isnull(@groupID,1),oilLevel=@oilLevel,
            [input power]=@inputpower,
            receivetime=@rectime,lastrcvTime=@rectime
            where tid=@tid
          end
      end
end  

if(@fix=0)
begin
  set @lat=-1
  set @lon=-1
  set @speed=-1
  set @course=-1	
  update tblatestlocation
  set lastRcvTime=@rectime 
  where tid=@tid
end

if exists (select alarmtypeid from tbselectedalarm where customerid=@customerID and (maskID&@status)>0)
begin
  insert into tbalarm (tid, license, AlarmtypeID, ack, lat, lon, speed, course, alarmTime) 
  select @tid, @license, alarmtypeid, 0, @lat, @lon, @speed, @course, @rectime 
  from tbSelectedAlarm 
  where customerID=@customerID and (maskID&@status>0)
end
GO
SET QUOTED_IDENTIFIER OFF 
GO
SET ANSI_NULLS ON 
GO

